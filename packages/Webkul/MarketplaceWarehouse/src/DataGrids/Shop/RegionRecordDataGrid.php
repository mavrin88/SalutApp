<?php

namespace Webkul\MarketplaceWarehouse\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\MarketplaceWarehouse\Repositories\RegionRepository;
use Webkul\MarketplaceWarehouse\Repositories\WarehouseRepository;
use Webkul\MarketplaceWarehouse\Repositories\PriceTypeRepository;
use Webkul\MarketplaceWarehouse\Repositories\DiscountRepository;
use Webkul\Product\Repositories\ProductRepository;

/**
 * Warehouse Assigned Receipts Data Grid class
 */
class RegionRecordDataGrid extends DataGrid
{   
    /**
    * @var integer
    */
    protected $index = 'id';

    /**
     * @var string
     */
    protected $sortOrder = 'asc'; //asc or desc

    /**
     * Create datagrid instance.
     *
     * @param  \Webkul\MarketplaceWarehouse\Repositories\RegionRepository $region
     * @param  \Webkul\Product\Repositories\ProductRepository $productRepository
     * @param  \Webkul\Inventory\Repositories\InventorySourceRepository  $inventorySourceRepository
     * @param  \Webkul\MarketplaceWarehouse\Repositories\PriceTypeRepository $priceType
     * @param  \Webkul\MarketplaceWarehouse\Repositories\DiscountRepository $discount
     * @param  \Webkul\Product\Repositories\ProductRepository $productRepository
     * @return void
     */
    public function __construct(
        protected RegionRepository $region,
        protected WarehouseRepository $warehouse,
        protected PriceTypeRepository $priceType,
        protected DiscountRepository $discount,
        protected ProductRepository $productRepository
    ) {
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

        $region = $this->region->find($id);

        $priceTypeID = $region->price_type_id;
       
        $queryBuilder = DB::table('warehouse_regions')
            ->leftJoin('warehouse_discount', 'warehouse_regions.id', '=', 'warehouse_discount.warehouse_region_id')
            ->join('product_flat', 'warehouse_discount.product_id', '=', 'product_flat.id')
            ->join('product_inventory_indices', 'product_flat.id', '=', 'product_inventory_indices.product_id')
            ->leftJoin('warehouse_prices', function ($join) use ($priceTypeID) {
                $join->on('product_flat.id', '=', 'warehouse_prices.product_id')
                    ->where('warehouse_prices.price_type_id', $priceTypeID);
            })
            ->where('warehouse_discount.warehouse_region_id', $region->id)
            ->addSelect(
                'warehouse_discount.id as id',
                'product_flat.sku as sku',
                'product_flat.name as name',
                'product_flat.price as purchase_price',
                'warehouse_discount.base_selling_price as base_selling_price',
                'warehouse_discount.discount as discount',
                'warehouse_discount.real_selling_price as real_selling_price',
                'product_inventory_indices.qty as qty',
            );

        $this->addFilter('id', 'warehouse_discount.id');
        $this->addFilter('sku', 'product_flat.sku');
        $this->addFilter('name', 'product_flat.name');
        $this->addFilter('purchase_price', 'product_flat.price');
        $this->addFilter('base_selling_price', 'warehouse_discount.base_selling_price');
        $this->addFilter('discount', 'warehouse_discount.discount');
        $this->addFilter('real_selling_price', 'warehouse_discount.real_selling_price');
        $this->addFilter('qty', 'product_inventory_indices.qty');
        
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
            'label'      => 'Id',
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
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'purchase_price',
            'label'      => 'Purchase Price',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'base_selling_price',
            'label'      => 'Base Selling Price',
            'type'       => 'number',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => false,
            'closure'    => function ($row) {
                return $row->base_selling_price ?? $row->purchase_price;
            },
        ]);

        $this->addColumn([
            'index'      => 'discount',
            'label'      => 'Discount',
            'type'       => 'number',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => false,
            'closure'    => function ($row) {
                if (is_null($row->discount)) {
                    return 0;
                }
                
                return $this->renderDiscountView($row);
            },
        ]);


        $this->addColumn([
            'index'      => 'real_selling_price',
            'label'      => 'Real Selling Price',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'qty',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.quantity'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    /**
     * Render discount view.
     *
     * @param  object  $row
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    private function renderDiscountView($row)
    {
        $discount = $this->discount->find($row->id);

        $product = $this->productRepository->find($discount->product_id);

        $totalDiscount = $row->discount;

        return view('marketplace_warehouse::shop.sellers.account.warehouse.region.datagrid.discount', compact('totalDiscount', 'product', 'discount'))->render();
    }
}
