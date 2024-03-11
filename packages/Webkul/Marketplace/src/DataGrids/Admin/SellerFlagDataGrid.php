<?php

namespace Webkul\Marketplace\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Seller Flag Data Grid class
 *
 * @author Amit kumar kesharwani <amitkumar.laravel358@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class SellerFlagDataGrid extends DataGrid
{
    /**
     *
     * @var integer
     */
    public $index = 'id';

    protected $sortOrder = 'desc'; //asc or desc

    protected $enableFilterMap = false;

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
        $queryBuilder = DB::table('marketplace_seller_flags')

            ->select('marketplace_seller_flags.id', 'marketplace_seller_flags.reason', 'marketplace_seller_flags.name', 'marketplace_seller_flags.email');

        $this->addFilter('reason', 'marketplace_seller_flags.reason');
        $this->addFilter('email', 'marketplace_seller_flags.email');
        $this->addFilter('name', 'marketplace_seller_flags.name');
        $this->addFilter('id', 'marketplace_seller_flags.id');
        $this->addFilter('created_at', 'marketplace_seller_flags.created_at');

        if (isset(request()->id)) {
            $this->seller = $this->sellerRepository->findOneWhere(['customer_id' => request()->id]);
            if (isset($this->seller)) {
                $queryBuilder->where('marketplace_seller_flags.seller_id', $this->seller->id);
            }
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
