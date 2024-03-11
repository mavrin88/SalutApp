<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class ProductReviewsDataGrid extends DataGrid
{
    /**
     * Index.
     *
     * @var string
     */
    protected $index = 'product_review_id';

    /**
     * Sort order.
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('product_reviews as pr')
            ->leftjoin('product_flat as pf', 'pr.product_id', '=', 'pf.product_id')
            ->join('marketplace_products as mp', 'mp.product_id', '=', 'pf.product_id')
            ->join('marketplace_sellers as ms', 'ms.id', '=', 'mp.marketplace_seller_id')
            ->select('pr.id as product_review_id', 'pr.title', 'pr.comment', 'pf.name as product_name', 'ms.shop_title as shop_title', 'pr.status as product_review_status', 'pr.rating', 'pr.created_at')
            ->where('channel', core()->getCurrentChannelCode())
            ->where('locale', app()->getLocale());

        $this->addFilter('product_review_id', 'pr.id');
        $this->addFilter('product_review_status', 'pr.status');
        $this->addFilter('product_name', 'pf.name');
        $this->addFilter('created_at', 'pr.created_at');

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
            'index'      => 'product_review_id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'title',
            'label'      => trans('admin::app.datagrid.title'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'comment',
            'label'      => trans('admin::app.datagrid.comment'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'rating',
            'label'      => trans('admin::app.customers.reviews.rating'),
            'type'       => 'number',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'product_name',
            'label'      => trans('admin::app.datagrid.product-name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'shop_title',
            'label'      => trans('marketplace::app.shop.sellers.account.profile.shop_title'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'product_review_status',
            'label'      => trans('admin::app.datagrid.status'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'width'      => '100px',
            'filterable' => true,
            'closure'    => function ($value) {
                if ($value->product_review_status == 'approved') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.datagrid.approved') . '</span>';
                } elseif ($value->product_review_status == "pending") {
                    return '<span class="badge badge-md badge-warning">' . trans('admin::app.datagrid.pending') . '</span>';
                } elseif ($value->product_review_status == "disapproved") {
                    return '<span class="badge badge-md badge-danger">' . trans('admin::app.datagrid.disapproved') . '</span>';
                }
            },
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.date'),
            'type'       => 'datetime',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
        ]);
    }
}
