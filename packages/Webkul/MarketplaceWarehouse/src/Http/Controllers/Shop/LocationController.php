<?php

namespace  Webkul\MarketplaceWarehouse\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Webkul\Checkout\Facades\Cart;

/**
 *  Lacation controller
 *
 */
class LocationController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_config = request('_config');
    }

    /**
     * Save Location of customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Redirect
     */
    public function saveLocation(Request $request)
    {
        $this->validate($request, [
            'location' => 'required',
        ]);

        Cart::deActivateCart();

        $request->session()->put('location', $request->input('location'));

        return redirect()->route('shop.home.index');
    }
}
