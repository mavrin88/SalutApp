<?php

namespace Webkul\Marketplace\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Order Data Grid class
 *
 * @author Amit Kumar Kesharwani <amitkumar.laravel358@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class OrderDataGrid extends DataGrid
{
    /**
     * @var integer
     */
    protected $index = 'order_id';

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
            ->select('orders.id', 'marketplace_orders.order_id', 'marketplace_orders.base_grand_total', 'marketplace_orders.grand_total', 'marketplace_orders.created_at', 'channel_name', 'marketplace_orders.status', 'orders.order_currency_code')
            ->addSelect(DB::raw('CONCAT(' . $table_prefix . 'orders.customer_first_name, " ",' . $table_prefix . 'orders.customer_last_name) as customer_name'), 'orders.increment_id')
            ->where('marketplace_orders.marketplace_seller_id', $seller->id);

        $this->addFilter('customer_name', DB::raw('CONCAT(' . $table_prefix . 'orders.customer_first_name, " ", ' . $table_prefix . 'orders.customer_last_name)'));
        $this->addFilter('id', 'orders.id');
        $this->addFilter('base_grand_total', 'marketplace_orders.base_grand_total');
        $this->addFilter('grand_total', 'marketplace_orders.grand_total');
        $this->addFilter('created_at', 'marketplace_orders.created_at');
        $this->addFilter('status', 'marketplace_orders.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'increment_id',
            'label'      => trans('marketplace::app.shop.sellers.account.sales.orders.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'base_grand_total',
            'label'      => trans('marketplace::app.shop.sellers.account.sales.orders.base-total'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                if (! is_null($row->base_grand_total)) {
                    $baseGrandPrice = core()->convertPrice($row->base_grand_total, $row->order_currency_code);

                    return core()->formatPrice($baseGrandPrice, $row->order_currency_code);
                }
            }
        ]);

        $this->addColumn([
            'index'      => 'grand_total',
            'label'      => trans('marketplace::app.shop.sellers.account.sales.orders.grand-total'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                if (! is_null($row->grand_total)) {
                    return core()->formatPrice($row->grand_total, $row->order_currency_code);
                }
            }
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('marketplace::app.shop.sellers.account.sales.orders.order-date'),
            'type'       => 'datetime',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('marketplace::app.shop.sellers.account.sales.orders.status'),
            'type'       => 'string',
            'sortable'   => false,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($row) {
                switch ($row->status) {
                    case "completed":
                        return '<span class="badge badge-md badge-success">' . trans("marketplace::app.shop.sellers.account.sales.orders.completed") . '</span>';
                        break;
                    case "processing":
                        return '<span class="badge badge-md badge-success">' . trans("marketplace::app.shop.sellers.account.sales.orders.processing") . '</span>';
                        break;
                    case "canceled":
                        return '<span class="badge badge-md badge-danger">' . trans("marketplace::app.shop.sellers.account.sales.orders.canceled") . '</span>';
                        break;
                    case "closed":
                        return '<span class="badge badge-md badge-info">' . trans("marketplace::app.shop.sellers.account.sales.orders.closed") . '</span>';
                        break;
                    case "pending":
                        return '<span class="badge badge-md badge-warning">' . trans("marketplace::app.shop.sellers.account.sales.orders.pending") . '</span>';
                        break;
                    case "pending_payment":
                        return '<span class="badge badge-md badge-warning">' . trans("marketplace::app.shop.sellers.account.sales.orders.pending-payment") . '</span>';
                        break;
                    case "fraud":
                        return '<span class="badge badge-md badge-danger">' . trans("marketplace::app.shop.sellers.account.sales.orders.fraud") . '</span>';
                        break;
                }
            }
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => trans("marketplace::app.shop.sellers.account.sales.orders.billed-to"),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'type'   => 'View',
            'route'  => 'marketplace.account.orders.view',
            'icon'   => 'icon eye-icon',
            'method' => 'GET',
            'title'  => 'View'
        ], true);
    }
}
