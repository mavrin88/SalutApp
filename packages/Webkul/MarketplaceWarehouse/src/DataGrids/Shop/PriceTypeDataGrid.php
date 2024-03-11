<?php

namespace Webkul\MarketplaceWarehouse\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Warehouse Receipts Data Grid class
 */
class PriceTypeDataGrid extends DataGrid
{
    /**
     * @var integer
     */
    protected $index = 'product_type_id';

    /**
     * @var string
     */
    protected $sortOrder = 'desc'; //asc or desc

    /**
     * Create datagrid instance.
     *
     * @param  \Webkul\Marketplace\Repositories\SellerRepository $sellerRepository
     * @return void
     */
    public function __construct(protected SellerRepository $sellerRepository) {
        parent::__construct();
    }

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $seller = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $queryBuilder = DB::table('warehouse_price_type')
            ->leftJoin('warehouse_prices', 'warehouse_price_type.id', '=', 'warehouse_prices.price_type_id')
            ->select(
                'warehouse_price_type.id as product_type_id',
                'warehouse_price_type.title as title',
                DB::raw('count(warehouse_prices.id) as product_count')
            )
            ->where('marketplace_seller_id', $seller->id)
            ->groupBy('warehouse_price_type.id', 'warehouse_price_type.title');

        $this->addFilter('product_type_id', 'warehouse_price_type.id');
        $this->addFilter('title', 'warehouse_price_type.title');
        $this->addFilter('product_count', 'warehouse_prices.id');
    
        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'product_type_id',
            'label'      => trans('marketplace_warehouse::app.shop.warehouse.price.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('marketplace_warehouse::app.shop.warehouse.price.price-title'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'product_count',
            'label'      => trans('marketplace_warehouse::app.shop.warehouse.price.product-count'),
            'type'       => 'number',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */    
    public function prepareActions()
    {
        $this->addAction([
            'title'     => trans('admin::app.datagrid.edit'),
            'method'    => 'GET',
            'route'     => 'marketplace_warehouse.user.warehouse.price.edit',
            'icon'      => 'icon pencil-lg-icon',
            'condition' => function () {                
                return true;
            },
        ], true);

        $this->addAction([
            'title'        => trans('admin::app.datagrid.delete'),
            'method'       => 'POST',
            'route'        => 'marketplace_warehouse.user.warehouse.price.remove',
            'confirm_text' => trans('ui::app.datagrid.mass-action.delete', ['resource' => 'warehouse']),
            'icon'         => 'icon trash-icon',
        ], true);

        $this->addAction([
            'title'  => trans('admin::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'marketplace_warehouse.user.warehouse.price.view',
            'icon'   => 'icon eye-icon', 
        ], true);
    }
}
