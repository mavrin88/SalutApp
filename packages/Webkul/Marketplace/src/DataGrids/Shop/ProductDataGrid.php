<?php

namespace Webkul\Marketplace\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Product Data Grid class
 *
 * @author Amit Kumar Kesharwani <amitkumar.laravel358@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductDataGrid extends DataGrid
{
    /**
     * @var integer
     */
    protected $index = 'product_id';

    /**
     * @var string
     */
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
        $queryBuilder = DB::table('product_flat')
            ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
            ->join('marketplace_products', 'product_flat.product_id', '=', 'marketplace_products.product_id')
            ->leftJoin('marketplace_sellers', 'marketplace_products.marketplace_seller_id', '=', 'marketplace_sellers.id')
            ->leftJoin('customers', 'marketplace_sellers.customer_id', '=', 'customers.id')
            ->addSelect(
                'products.type as product_type',
                'marketplace_products.id as marketplace_product_id',
                'product_flat.product_id',
                'product_flat.sku',
                'product_flat.name as name',
                'product_flat.product_number',
                'marketplace_products.is_owner',
                'marketplace_products.is_approved',
                DB::raw('CONCAT(' . $table_prefix . 'customers.first_name, " ", ' . $table_prefix . 'customers.last_name) as seller_name'),
                DB::raw('(CASE WHEN ' . $table_prefix . 'marketplace_products.is_owner = 1 THEN ' . $table_prefix . 'product_flat.price ELSE ' . $table_prefix . 'marketplace_products.price END) AS price')
            )
            ->where('marketplace_products.marketplace_seller_id', $seller->id)


            ->where('channel', core()->getCurrentChannelCode())
            ->where('locale', app()->getLocale())
            ->distinct();

        $queryBuilder = $queryBuilder->leftJoin('product_inventories', function ($qb) {

            $seller = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id);

            $qb->on('product_flat.product_id', 'product_inventories.product_id')
                ->where('product_inventories.vendor_id', '=', $seller->id);
        });

        $queryBuilder
            ->groupBy('product_flat.product_id')
            ->addSelect(DB::raw('SUM(' . $table_prefix . 'product_inventories.qty) as quantity'));

        $this->addFilter('sku', 'product_flat.sku');
        $this->addFilter('product_id', 'product_flat.product_id');
        $this->addFilter('product_number', 'product_flat.product_number');
        $this->addFilter('product_type', 'products.type');
        $this->addFilter('name', 'product_flat.name');
        $this->addFilter('is_approved', 'marketplace_products.is_approved');
        $this->addFilter('price', DB::raw('(CASE WHEN marketplace_products.is_owner = 1 THEN product_flat.price ELSE marketplace_products.price END)'));
        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'product_id',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'product_number',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.product-number'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'sku',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.sku'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'product_type',
            'label'      => trans('admin::app.datagrid.type'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'is_owner',
            'label'      => trans('marketplace::app.admin.sellers.owner'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($data) {
                if ($data->is_owner) {
                    return trans('marketplace::app.admin.sellers.add-title');
                }

                return trans('admin::app.catalog.attributes.admin');
            }
        ]);

        $this->addColumn([
            'index'      => 'price',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.price'),
            'type'       => 'price',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($row) {
                return number_format($row->price, 2);
            }
        ]);

        $this->addColumn([
            'index'      => 'quantity',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.quantity'),
            'type'       => 'number',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => false
        ]);

        $this->addColumn([
            'index'      => 'is_approved',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.is-approved'),
            'type'       => 'boolean',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($row) {
                if ($row->is_approved == 1) {
                    return '<span class="badge badge-md badge-success">' . trans('marketplace::app.shop.sellers.account.catalog.products.yes') . '</span>';
                }

                return '<span class="badge badge-md badge-danger">' . trans('marketplace::app.shop.sellers.account.catalog.products.no') . '</span>';
            }
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'type'      => 'Edit',
            'method'    => 'GET',
            'route'     => 'marketplace.account.products.edit',
            'icon'      => 'icon pencil-lg-icon',
            'title'     => trans('ui::app.datagrid.edit'),
            'condition' => function ($row) {
                if (
                    $row->product_type == 'configurable' &&
                    $row->is_owner == 0
                ) {
                    return false;
                }

                return true;
            },
        ], true);

        $this->addAction([
            'type'         => 'delete',
            'method'       => 'POST',
            'route'        => 'marketplace.account.products.delete',
            'icon'         => 'icon trash-icon',
            'title'        => trans('admin::app.datagrid.delete'),
            'condition'    => function ($row) {
                if (
                    $row->product_type == 'configurable' &&
                    $row->is_owner == 0
                ) {
                    return false;
                }

                return true;
            },
        ], true);

        $this->addAction([
            'title'     => trans('admin::app.datagrid.copy'),
            'method'    => 'GET',
            'route'     => 'seller.catalog.products.copy',
            'icon'      => 'icon copy-icon',
            'condition' => function ($row) {
                if (
                    $row->product_type == 'configurable' &&
                    $row->is_owner == 0
                ) {
                    return false;
                }

                return true;
            },
        ], true);
    }

    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('marketplace::app.shop.sellers.account.catalog.products.delete'),
            'action' => route('marketplace.account.products.massdelete'),
            'method' => 'POST'
        ], true);
    }
}
