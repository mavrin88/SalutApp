<?php

namespace Webkul\MarketplaceWarehouse\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;


/**
 * Delivery Type Data Grid class
 */
class DeliveryTypeDataGrid extends DataGrid
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
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('warehouse_delivery_type')
            ->select('warehouse_delivery_type.id', 'warehouse_delivery_type.title');

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
            'label'      => trans('marketplace_warehouse::app.admin.layouts.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('marketplace_warehouse::app.admin.delivery-type.title'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'type'   => 'Edit',
            'method' => 'GET',
            'route'  => 'admin.warehouse.delivery_type.edit',
            'icon'   => 'icon pencil-lg-icon',
            'title'  => trans('admin::app.datagrid.edit'),
        ]);

        $this->addAction([
            'type'         => 'Delete',
            'method'       => 'GET',
            'route'        => 'admin.warehouse.delivery_type.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete'),
            'icon'         => 'icon trash-icon',
            'title'        => trans('admin::app.datagrid.delete'),
        ]);
    }

    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('marketplace::app.admin.sellers.delete'),
            'action' => route('admin.warehouse.delivery_type.mass-delete'),
            'method' => 'POST',
            'title'  => trans('admin::app.datagrid.delete'),
        ]);
    }
}
