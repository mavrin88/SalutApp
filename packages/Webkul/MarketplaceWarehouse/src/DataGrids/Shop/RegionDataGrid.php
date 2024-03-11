<?php

namespace Webkul\MarketplaceWarehouse\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

/**
 * Warehouse Region Data Grid class
 */
class RegionDataGrid extends DataGrid
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
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $seller_id = auth()->guard('customer')->user()->id;

        $queryBuilder = DB::table('warehouse_regions')
            ->leftJoin('warehouses', 'warehouse_regions.warehouse_id', '=', 'warehouses.id')
            ->where('warehouses.marketplace_seller_id', $seller_id)
            ->addSelect(
                'warehouse_regions.id as id',
                'warehouse_regions.region_name as name',
                'warehouses.warehouse_name as warehouse',
            );

        $this->addFilter('id', 'warehouse_regions.id');
        $this->addFilter('name', 'warehouse_regions.region_name');
        $this->addFilter('warehouse', 'warehouses.warehouse_name');
        
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
            'label'      => 'ID',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'name',
            'label'      => 'Name',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'warehouse',
            'label'      => 'Warehouse',
            'type'       => 'number',
            'searchable' => false,
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
            'route'     => 'marketplace_warehouse.user.warehouse.region.edit',
            'icon'      => 'icon pencil-lg-icon',
            'condition' => function () {                
                return true;
            },
        ], true);

        $this->addAction([
            'title'        => trans('admin::app.datagrid.delete'),
            'method'       => 'POST',
            'route'        => 'marketplace_warehouse.user.warehouse.region.remove',
            'confirm_text' => trans('ui::app.datagrid.mass-action.delete', ['resource' => 'region']),
            'icon'         => 'icon trash-icon',
        ], true);

        $this->addAction([
            'title'  => trans('admin::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'marketplace_warehouse.user.warehouse.region.view',
            'icon'   => 'icon eye-icon',
        ], true);
    }
}
