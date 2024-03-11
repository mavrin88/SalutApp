<?php

namespace Webkul\MarketplaceWarehouse\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Warehouse Receipts Data Grid class
 */
class PriceRecordDataGrid extends DataGrid
{
    /**
     * @var integer
     */
    protected $index = 'id';

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
        $id = request()->route('id');

        $queryBuilder = DB::table('warehouse_price_type')
            ->leftJoin('warehouse_prices', 'warehouse_price_type.id', '=', 'warehouse_prices.price_type_id')
            ->join('product_flat', 'warehouse_prices.product_id', '=', 'product_flat.product_id')
            ->where('warehouse_price_type.id', $id)
            ->addSelect(
                'warehouse_prices.id as id',
                'product_flat.name as name',
                'product_flat.sku as sku',
                'warehouse_prices.price as price',
            );

        $this->addFilter('id', 'warehouse_prices.id');
        $this->addFilter('name', 'product_flat.name');
        $this->addFilter('sku', 'product_flat.sku');
        $this->addFilter('price', 'warehouse_prices.price');
    
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
            'index'      => 'id',
            'label'      => trans('marketplace_warehouse::app.shop.warehouse.price.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'sku',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.sku'),
            'type'       => 'number',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'price',
            'label'      => trans('marketplace_warehouse::app.shop.warehouse.price.amt'),
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
            'title'        => trans('admin::app.datagrid.delete'),
            'method'       => 'POST',
            'route'        => 'marketplace_warehouse.user.warehouse.price.delete',
            'confirm_text' => trans('ui::app.datagrid.mass-action.delete', ['resource' => 'warehouse']),
            'icon'         => 'icon trash-icon',
        ], true);
    }
}
