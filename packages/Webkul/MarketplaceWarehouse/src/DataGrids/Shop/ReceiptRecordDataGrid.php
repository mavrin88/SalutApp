<?php

namespace Webkul\MarketplaceWarehouse\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

/**
 * Warehouse Assigned Receipts Data Grid class
 */
class ReceiptRecordDataGrid extends DataGrid
{   
    /**
    * @var integer
    */
   protected $index = 'sku';

    /**
     * @var string
     */
    protected $sortOrder = 'asc'; //asc or desc

    /**
     * Create datagrid instance.
     *
     * @return void
     */
    public function __construct() {
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

        $queryBuilder = DB::table('receipts')
            ->leftJoin('qty_logs', 'receipts.id', '=', 'qty_logs.receipt_id')
            ->join('product_flat', 'qty_logs.associated_product_id', '=', 'product_flat.product_id')
            ->where('receipts.id', $id)
            ->addSelect(
                'product_flat.product_id as product_id',
                'product_flat.sku as sku',
                'product_flat.name as name',
                'qty_logs.qty as qty',
            );

        $this->addFilter('product_id', 'product_flat.product_id');
        $this->addFilter('sku', 'product_flat.sku');
        $this->addFilter('name', 'product_flat.name');
        $this->addFilter('qty', 'qty_logs.qty');
      
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
            'index'      => 'sku',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.sku'),
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
            'index'      => 'qty',
            'label'      => trans('marketplace::app.shop.sellers.account.catalog.products.quantity'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }
}
