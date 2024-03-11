<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Order Data Grid class
 *
 * @author Amit kumar kesharwani <amitkumar.laravel358@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class OrderDataGrid extends DataGrid
{
    /**
     * @var integer
     */
    protected $index = 'order_id';

    protected $sortOrder = 'desc';

    /**
     * Seller object
     *
     * @var Object
     */
    protected $seller;

    /**
     * Create a new repository instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository $sellerRepository
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository
    ) {
        parent::__construct();
    }

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('marketplace_orders')
            ->leftJoin('orders', 'marketplace_orders.order_id', '=', 'orders.id')
            ->leftJoin('marketplace_transactions', 'marketplace_orders.id', '=', 'marketplace_transactions.marketplace_order_id')
            ->select('orders.id', 'marketplace_orders.order_id', 'marketplace_orders.base_sub_total', 'marketplace_orders.base_grand_total', 'marketplace_orders.base_commission', 'marketplace_orders.base_seller_total', 'marketplace_orders.base_seller_total_invoiced', 'marketplace_orders.created_at', 'marketplace_orders.status', 'is_withdrawal_requested', 'seller_payout_status', 'marketplace_orders.marketplace_seller_id', 'marketplace_orders.base_discount_amount')
            ->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'orders.customer_first_name, " ", ' . DB::getTablePrefix() . 'orders.customer_last_name) as customer_name'), 'orders.increment_id')
            ->addSelect(DB::raw('SUM(' . DB::getTablePrefix() . 'marketplace_transactions.base_total) as total_paid'))
            ->groupBy('marketplace_orders.id');

        if (request()->id) {
            $this->seller = $this->sellerRepository->find(request()->id);

            $queryBuilder->where('marketplace_orders.marketplace_seller_id', $this->seller->id);
        } else {
            $queryBuilder->leftJoin('marketplace_sellers', 'marketplace_orders.marketplace_seller_id', '=', 'marketplace_sellers.id')
                ->leftJoin('customers', 'marketplace_sellers.customer_id', '=', 'customers.id')
                ->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'customers.first_name, " ", ' . DB::getTablePrefix() . 'customers.last_name) as seller_name'));

            $this->addFilter('seller_name', DB::raw('CONCAT(' . DB::getTablePrefix() . 'customers.first_name, " ", ' . DB::getTablePrefix() . 'customers.last_name)'));
        }

        $this->addFilter('customer_name', DB::raw('CONCAT(' . DB::getTablePrefix() . 'orders.customer_first_name, " ", ' . DB::getTablePrefix() . 'orders.customer_last_name)'));
        $this->addFilter('base_grand_total', 'marketplace_orders.base_grand_total');
        $this->addFilter('status', 'marketplace_orders.status');
        $this->addFilter('created_at', 'marketplace_orders.created_at');
        $this->addFilter('base_discount_amount', 'marketplace_orders.base_discount_amount');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'increment_id',
            'label'      => trans('marketplace::app.admin.orders.order-id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => trans('marketplace::app.admin.orders.billed-to'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('marketplace::app.admin.orders.status'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($row) {
                switch ($row->status) {
                    case "processing":
                        return '<span class="badge badge-md badge-success">' . trans("marketplace::app.admin.orders.processing") . '</span>';
                        break;
                    case "completed":
                        return '<span class="badge badge-md badge-success">' . trans("marketplace::app.admin.orders.completed") . '</span>';
                        break;
                    case "canceled":
                        return '<span class="badge badge-md badge-danger">' . trans("marketplace::app.admin.orders.canceled") . '</span>';
                        break;
                    case "closed":
                        return '<span class="badge badge-md badge-info">' . trans("marketplace::app.admin.orders.closed") . '</span>';
                        break;
                    case "pending":
                        return '<span class="badge badge-md badge-warning">' . trans("marketplace::app.admin.orders.pending") . '</span>';
                        break;
                    case "pending_payment":
                        return '<span class="badge badge-md badge-warning">' . trans("marketplace::app.admin.orders.pending-payment") . '</span>';
                        break;
                    case "fraud":
                        return '<span class="badge badge-md badge-danger">' . trans("marketplace::app.admin.orders.fraud") . '</span>';
                        break;
                }
            }
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('marketplace::app.admin.orders.order-date'),
            'type'       => 'datetime',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true
        ]);

        if (! request()->id) {
            $this->addColumn([
                'index'      => 'seller_name',
                'label'      => trans('marketplace::app.admin.orders.seller-name'),
                'type'       => 'string',
                'sortable'   => true,
                'searchable' => true,
                'filterable' => true
            ]);
        }

        $this->addColumn([
            'index'      => 'base_remaining_total',
            'label'      => trans('marketplace::app.admin.orders.remaining-total'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                if (! is_null($row->total_paid)) {
                    return core()->formatBasePrice($row->base_seller_total_invoiced - $row->total_paid);
                }

                return core()->formatBasePrice($row->base_seller_total_invoiced);
            }
        ]);

        $this->addColumn([
            'index'      => 'pay',
            'label'      => trans('marketplace::app.admin.orders.pay'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                if ($row->seller_payout_status == 'paid') {
                    return trans('marketplace::app.admin.orders.already-paid');
                } else if ($row->seller_payout_status == 'refunded') {
                    return trans('marketplace::app.admin.orders.refunded');
                } else {
                    $remaining = !is_null($row->total_paid) ? $row->base_seller_total_invoiced - $row->total_paid : $row->base_seller_total_invoiced;

                    if ((float) $remaining) {
                        return '<button class="btn btn-sm btn-primary pay-btn" data-id="' . $row->id . '" seller-id="' . $row->marketplace_seller_id . '">' . trans('marketplace::app.admin.orders.pay') . '</button>';
                    } elseif ($row->status == "canceled") {
                        return '<span class="badge badge-md badge-danger">' . trans('marketplace::app.admin.orders.order-canceled') . '</span>';
                    }

                    return '<span class="badge badge-md badge-warning">' . trans('marketplace::app.admin.orders.invoice-pending') . '</span>';
                }
            }
        ]);

        $this->addColumn([
            'index'      => 'base_grand_total',
            'label'      => trans('marketplace::app.admin.orders.grand-total'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'base_commission',
            'label'      => trans('marketplace::app.admin.orders.commission'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'base_discount_amount',
            'label'      => trans('marketplace::app.admin.orders.discount'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'base_seller_total',
            'label'      => trans('marketplace::app.admin.orders.seller-total'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'base_seller_total_invoiced',
            'label'      => trans('marketplace::app.admin.orders.seller-total-invoiced'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'total_paid',
            'label'      => trans('marketplace::app.admin.orders.total-paid'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'type'   => 'View',
            'route'  => 'admin.sales.orders.view',
            'icon'   => 'icon eye-icon',
            'method' => 'GET',
            'title'  => 'View'
        ], true);
    }
}
