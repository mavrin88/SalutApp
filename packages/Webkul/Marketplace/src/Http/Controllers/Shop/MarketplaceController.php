<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

/**
 * Marketplace page controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class MarketplaceController extends Controller
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
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! core()->getConfigData('marketplace.settings.general.status')) {
            abort(404);
        }

        return view($this->_config['view']);
    }
}
