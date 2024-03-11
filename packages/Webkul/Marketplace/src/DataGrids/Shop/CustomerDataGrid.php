<?php

namespace Webkul\Marketplace\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Customer Data Grid class
 *
 * @author Amit Kumar Kesharwani <amitkumar.laravel358@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CustomerDataGrid extends DataGrid
{
    /**
     * @var integer
     */
    protected $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

    /**
     * Create a new repository instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository $sellerRepository
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository
    ) {
        parent::__construct();
    }

    public function prepareQueryBuilder()
    {
        $seller = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $table_prefix = DB::getTablePrefix();

        $queryBuilder = DB::table('marketplace_orders')
            ->leftJoin('orders', 'marketplace_orders.order_id', '=', 'orders.id')
            ->rightJoin('addresses', 'marketplace_orders.order_id', 'addresses.order_id')
            ->select('orders.id', 'orders.customer_id',  'marketplace_orders.order_id', DB::raw('sum(marketplace_orders.base_grand_total) as base_grand_total'), 'marketplace_orders.grand_total', 'marketplace_orders.created_at', 'channel_name', 'orders.status', 'orders.order_currency_code')
            ->addSelect(DB::raw('CONCAT(' . $table_prefix . 'orders.customer_first_name, " ", ' . $table_prefix . 'orders.customer_last_name) as customer_name'), 'orders.increment_id', 'orders.customer_email', DB::raw('CONCAT(' . $table_prefix . 'addresses.	address1, " " ,' . $table_prefix . 'addresses.state, " " , ' . $table_prefix . 'addresses.country, " ", ' . $table_prefix . 'addresses.postcode ) as address'), 'addresses.gender', 'addresses.phone', DB::raw('count(*) as order_count'))
            ->where('addresses.address_type', 'order_billing')
            ->where('marketplace_orders.marketplace_seller_id', $seller->id)
            ->groupBy('orders.customer_email');

        $this->addFilter('customer_name', DB::raw('CONCAT(' . $table_prefix . 'orders.customer_first_name, " ", ' . $table_prefix . 'orders.customer_last_name)'));
        $this->addFilter('customer_email', 'orders.customer_email');
        $this->addFilter('phone', 'addresses.phone');
        $this->addFilter('gender', 'addresses.gender');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => trans("marketplace::app.shop.sellers.account.sales.orders.customer-name"),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'customer_email',
            'label'      => trans("marketplace::app.shop.sellers.account.sales.orders.email"),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'phone',
            'label'      => trans("marketplace::app.shop.sellers.account.sales.orders.phone"),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'gender',
            'label'      => trans("marketplace::app.shop.sellers.account.sales.orders.gender"),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'address',
            'label'      => trans("marketplace::app.shop.sellers.account.sales.orders.address"),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'filterable' => false
        ]);

        $this->addColumn([
            'index'      => 'base_grand_total',
            'label'      => trans('marketplace::app.shop.sellers.account.sales.orders.base-total'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false
        ]);

        $this->addColumn([
            'index'      => 'order_count',
            'label'      => trans('marketplace::app.shop.sellers.account.sales.orders.order-count'),
            'type'       => 'integer',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
            'closure'    => function ($row) {
                return '<a href="' . route('marketplace.account.customers.order.index', encrypt($row->customer_email)) . '">' . $row->order_count . '</a>';
            }
        ]);
    }
}
