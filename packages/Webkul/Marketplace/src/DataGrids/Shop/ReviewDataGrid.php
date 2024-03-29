<?php

namespace Webkul\Marketplace\DataGrids\Shop;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Review Data Grid class
 *
 * @author Amit Kumar Kesharwani <amitkumar.laravel358@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ReviewDataGrid extends DataGrid
{
    /**
     * @var integer
     */
    protected $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

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
        $seller = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $table_prefix = DB::getTablePrefix();

        $queryBuilder = DB::table('marketplace_seller_reviews')
            ->select('marketplace_seller_reviews.id', 'rating', 'comment', 'marketplace_seller_reviews.status')
            ->addSelect(DB::raw('CONCAT(' . $table_prefix . 'customers.first_name, " ", ' . $table_prefix . 'customers.last_name) as customer_name'))
            ->leftJoin('customers', 'marketplace_seller_reviews.customer_id', '=', 'customers.id')
            ->where('marketplace_seller_reviews.marketplace_seller_id', $seller->id);

        $this->addFilter('customer_name', DB::raw('CONCAT(' . $table_prefix . 'customers.first_name, " ", ' . $table_prefix . 'customers.last_name)'));
        $this->addFilter('id', 'marketplace_seller_reviews.id');
        $this->addFilter('status', 'marketplace_seller_reviews.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('marketplace::app.shop.sellers.account.reviews.id'),
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'customer_name',
            'label'      => trans('marketplace::app.shop.sellers.account.reviews.customer-name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'rating',
            'label'      => trans('marketplace::app.shop.sellers.account.reviews.rating'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'comment',
            'label'      => trans('marketplace::app.shop.sellers.account.reviews.comment'),
            'type'       => 'string',
            'sortable'   => false,
            'searchable' => false,
            'filterable' => true
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('marketplace::app.shop.sellers.account.reviews.status'),
            'type'       => 'string',
            'sortable'   => false,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($row) {
                switch ($row->status) {
                    case "pending":
                        return '<span class="badge badge-md badge-warning">' . trans("marketplace::app.shop.sellers.account.reviews.pending") . '</span>';
                        break;
                    case "approved":
                        return '<span class="badge badge-md badge-success">' . trans("marketplace::app.shop.sellers.account.reviews.approved") . '</span>';
                        break;
                    case "unapproved":
                        return '<span class="badge badge-md badge-danger">' . trans("marketplace::app.shop.sellers.account.reviews.unapproved") . '</span>';
                        break;
                }
            }
        ]);
    }
}
