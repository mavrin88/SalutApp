<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

/**
 * Product Flag Reason Data Grid class
 *
 * @author Amit kumar kesharwani <amitkumar.laravel358@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductFlagReasonDataGrid extends DataGrid
{
    /**
     *
     * @var integer
     */
    public $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('marketplace_product_flag_reasons')
            ->select('marketplace_product_flag_reasons.id', 'marketplace_product_flag_reasons.reason', 'marketplace_product_flag_reasons.status');

        $this->addFilter('reason', 'marketplace_product_flag_reasons.reason');
        $this->addFilter('status', 'marketplace_product_flag_reasons.status');
        $this->addFilter('id', 'marketplace_product_flag_reasons.id');
        $this->addFilter('created_at', 'marketplace_product_flag_reasons.created_at');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('marketplace::app.admin.sellers.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'reason',
            'label'      => trans('marketplace::app.admin.products.flag.reason'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('marketplace::app.admin.products.flag.status'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                if ($row->status  == 1) {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.datagrid.active') . '</span>';
                }

                return '<span class="badge badge-md badge-danger">' . trans('admin::app.datagrid.inactive') . '</span>';
            },
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'type'   => 'Edit',
            'method' => 'GET',
            'route'  => 'marketplace.admin.product.flag.reason.edit',
            'icon'   => 'icon pencil-lg-icon',
            'title'  => trans('admin::app.datagrid.edit'),
        ]);

        $this->addAction([
            'type'         => 'Delete',
            'method'       => 'GET',
            'route'        => 'marketplace.admin.product.flag.reason.delete',
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
            'action' => route('marketplace.admin.product.flag.reason.mass-delete'),
            'method' => 'POST',
            'title'  => trans('admin::app.datagrid.delete'),
        ]);
    }
}
