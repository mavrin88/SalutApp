<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

/**
 * Seller Data Grid class
 *
 * @author Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class SellerDataGrid extends DataGrid
{
    /**
     *
     * @var integer
     */
    public $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

    public function prepareQueryBuilder()
    {
        $table_prefix = DB::getTablePrefix();

        if ((core()->getConfigData('marketplace.settings.seller_flag.enable'))) {

            $queryBuilder = DB::table('marketplace_sellers')
                ->leftJoin('customers', 'marketplace_sellers.customer_id', '=', 'customers.id')
                ->leftJoin('marketplace_seller_flags', 'marketplace_sellers.id', '=', 'marketplace_seller_flags.seller_id')

                ->addSelect('marketplace_sellers.id', 'marketplace_sellers.created_at', 'customers.email', 'marketplace_sellers.is_approved', 'marketplace_sellers.customer_id', DB::raw('CONCAT(' . $table_prefix . 'customers.first_name, " ", ' . $table_prefix . 'customers.last_name) as customer_name'), 'marketplace_sellers.url')->selectRaw('count(' . $table_prefix . 'marketplace_seller_flags.id) as flags')->groupBy('marketplace_sellers.id');

            $this->addFilter('customer_name', DB::raw('CONCAT(' . $table_prefix . 'customers.first_name, " ", ' . $table_prefix . 'customers.last_name)'));
            $this->addFilter('id', 'marketplace_sellers.id');
            $this->addFilter('email', 'customers.email');
            $this->addFilter('created_at', 'marketplace_sellers.created_at');
        } else {
            $queryBuilder = DB::table('marketplace_sellers')
                ->leftJoin('customers', 'marketplace_sellers.customer_id', '=', 'customers.id')

                ->addSelect('marketplace_sellers.id', 'marketplace_sellers.created_at', 'customers.email', 'marketplace_sellers.is_approved', 'marketplace_sellers.customer_id', DB::raw('CONCAT(customers.first_name, " ", customers.last_name) as customer_name'), 'marketplace_sellers.url');

            $this->addFilter('customer_name', DB::raw('CONCAT(' . $table_prefix . 'customers.first_name, " ", ' . $table_prefix . 'customers.last_name)'));
            $this->addFilter('email', 'customers.email');
            $this->addFilter('id', 'marketplace_sellers.id');
            $this->addFilter('created_at', 'marketplace_sellers.created_at');
        }

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
            'index'      => 'customer_name',
            'label'      => trans('marketplace::app.admin.sellers.seller-name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return '<a href="' . route('admin.customer.edit', $row->customer_id) . '">' . $row->customer_name . '</a>';
            }
        ]);

        if ((core()->getConfigData('marketplace.settings.seller_flag.enable'))) {
            $this->addColumn([
                'index'      => 'flags',
                'label'      => trans('marketplace::app.admin.flag.title'),
                'type'       => 'integer',
                'searchable' => false,
                'sortable'   => true,
                'filterable' => false,
                'closure'    => function ($row) {
                    return '<a href="' . route('admin.marketplace.seller.edit', $row->id) . '">' . $row->flags . '</a>';
                }
            ]);
        }

        $this->addColumn([
            'index'      => 'email',
            'label'      => trans('marketplace::app.admin.sellers.seller-email'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'url',
            'label'      => trans('marketplace::app.admin.sellers.seller-profile'),
            'type'       => 'integer',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
            'closure'    => function ($row) {
                return '<a target="_blank" href="' . route('marketplace.seller.show', $row->url) . '">' . trans('marketplace::app.admin.sellers.view-seller-profile') . '</a>';
            }
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('marketplace::app.admin.sellers.created-at'),
            'type'       => 'datetime',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'product',
            'label'      => trans('marketplace::app.admin.sellers.product'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                return '<a href = "' . route('admin.marketplace.seller.product.search', $row->id) . '" class="btn btn-sm btn-primary pay-btn" name="seller_id" value="' . $row->id . '">' . trans('marketplace::app.admin.sellers.add-product') . '</a>';
            }
        ]);

        $this->addColumn([
            'index'      => 'is_approved',
            'label'      => trans('marketplace::app.admin.sellers.is-approved'),
            'type'       => 'boolean',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($row) {
                return $this->renderApproveUnapproveView($row);
            }
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'type'   => 'edit',
            'method' => 'GET',
            'route'  => 'admin.marketplace.seller.edit',
            'icon'   => 'icon pencil-lg-icon',
            'title'  => 'Edit'
        ]);

        $this->addAction([
            'title'        => trans('admin::app.datagrid.delete'),
            'method'       => 'POST',
            'route'        => 'admin.marketplace.sellers.delete',
            'confirm_text' => trans('ui::app.datagrid.massaction.delete', ['resource' => 'Seller']),
            'icon'         => 'icon trash-icon',
        ]);
    }

    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('marketplace::app.admin.sellers.delete'),
            'action' => route('admin.marketplace.sellers.massdelete'),
            'method' => 'POST'
        ]);

        $this->addMassAction([
            'type'    => 'update',
            'label'   => trans('marketplace::app.admin.sellers.update'),
            'action'  => route('admin.marketplace.sellers.massupdate'),
            'method'  => 'POST',
            'options' => [
                trans('marketplace::app.admin.sellers.approve') => 1,
                trans('marketplace::app.admin.sellers.unapprove') => 0
            ]
        ]);
    }

    /**
     * Render seller approve/unapprove view.
     *
     * @param  object  $row
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    private function renderApproveUnapproveView($row)
    {
        $seller = app(\Webkul\Marketplace\Repositories\SellerRepository::class)->find($row->id);

        return view('marketplace::admin.sellers.approve-unapprove-seller', compact('seller'))->render();
    }
}
