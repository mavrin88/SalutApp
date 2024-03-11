<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account;

use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\DataGrids\Shop\CustomerDataGrid;

/**
 * Marketplace review controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CustomerController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     *
     * @var mixed
     */
    protected $sellerRepository;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository $sellerRepository
     * @return void
     */
    public function __construct(
        SellerRepository $sellerRepository
    ) {
        $this->sellerRepository = $sellerRepository;

        $this->_config = request('_config');
    }

    /**
     * Method to populate the seller review page which will be populated.
     *
     * @return Mixed
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(CustomerDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Method to populate the seller customer order page which will be populated.
     *
     * @return Mixed
     */
    public function orders($customerId)
    {
        return view($this->_config['view'], compact('customerId'));
    }
}
