<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Webkul\Marketplace\Http\Requests\CustomerReviewForm;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\ReviewRepository;

/**
 * Marketplace review controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ReviewController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * SellerRepository object
     *
     * @var object
     */
    protected $seller;

    /**
     * ReviewRepository object
     *
     * @var object
     */
    protected $review;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository $seller
     * @param  Webkul\Marketplace\Repositories\ReviewRepository $review
     * @return void
     */
    public function __construct(
        ReviewRepository $review,
        SellerRepository $seller
    ) {
        $this->_config = request('_config');

        $this->seller = $seller;

        $this->review = $review;
    }

    /**
     * Method to populate the seller review page which will be populated.
     *
     * @param  string  $url
     * @return Mixed
     */
    public function index($url)
    {
        $seller = $this->seller->findByUrlOrFail($url);

        return view($this->_config['view'], compact('seller'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function create($url)
    {
        $seller = $this->seller->findByUrlOrFail($url);

        return view($this->_config['view'], compact('seller'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerReviewForm $form,  $url)
    {
        $seller = $this->seller->findByUrlOrFail($url);

        $data = request()->input();

        $data['customer_id'] = auth()->guard('customer')->user()->id;

        if ($data['customer_id'] == $seller->customer_id) {
            session()->flash('error', __('marketplace::app.shop.review.self-review'));

            return redirect()->back();
        }

        $data['status'] = 'pending';

        $data['marketplace_seller_id'] = $seller->id;

        $this->review->create($data);

        session()->flash('success', __('marketplace::app.shop.review.success-msg'));

        return redirect()->route($this->_config['redirect'], ['url' => $url]);
    }
}
