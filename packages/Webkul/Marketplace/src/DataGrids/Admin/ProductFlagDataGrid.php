<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

/**
 * Product Flag Data Grid class
 *
 * @author Amit kumar kesharwani <amitkumar.laravel358@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductFlagDataGrid extends DataGrid
{
    /**
     *
     * @var integer
     */
    public $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('marketplace_product_flags')
            ->leftJoin('product_flat', 'marketplace_product_flags.product_id', '=', 'product_flat.product_id')
            ->select('marketplace_product_flags.id', 'marketplace_product_flags.reason', 'marketplace_product_flags.product_id', 'marketplace_product_flags.name', 'marketplace_product_flags.email')
            ->where('marketplace_product_flags.product_id', request()->id)
            ->where('product_flat.locale', core()->getCurrentLocale()->code);

        $this->addFilter('reason', 'marketplace_product_flags.reason');
        $this->addFilter('email', 'marketplace_product_flags.email');
        $this->addFilter('name', 'marketplace_product_flags.name');
        $this->addFilter('id', 'marketplace_product_flags.id');
        $this->addFilter('created_at', 'marketplace_product_flags.created_at');

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
            'index'      => 'name',
            'label'      => trans('marketplace::app.admin.flag.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'email',
            'label'      => trans('marketplace::app.admin.flag.email'),
            'type'       => 'string',
            'searchable' => true,
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
    }
}
