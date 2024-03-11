<?php

namespace Webkul\MarketplaceWarehouse\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

/**
 * Warehouse Receipts Data Grid class
 */
class ReceiptDataGrid extends DataGrid
{
    /**
     * @var integer
     */
    protected $index = 'receipt_id';

    /**
     * @var string
     */
    protected $sortOrder = 'desc'; //asc or desc

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

        $seller_id = auth()->guard('customer')->user()->id;

        $queryBuilder = DB::table('receipts')
            ->leftJoin('warehouses', 'receipts.warehouse_id', '=', 'warehouses.id')
            ->where('warehouses.marketplace_seller_id', $seller_id)
            ->addSelect(
                'receipts.id as receipt_id',
                'receipts.title as title',
                'receipts.created_at as created_at',
            );

        if ($id) {
            $queryBuilder->where('receipts.warehouse_id', $id);
        }

        $this->addFilter('receipt_id', 'receipts.id');
        $this->addFilter('title', 'receipts.title');
        $this->addFilter('created_at', 'receipts.created_at');

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
            'index'      => 'receipt_id',
            'label'      => trans('marketplace_warehouse::app.shop.warehouse.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('marketplace_warehouse::app.shop.warehouse.receipt-and-withdrawal.products.title'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('marketplace_warehouse::app.shop.warehouse.date'),
            'type'       => 'datetime',
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
            'title'  => trans('admin::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'marketplace_warehouse.user.warehouse.receipts-and-withdrawals.view',
            'icon'   => 'icon eye-icon',
        ], true);
    }
}
