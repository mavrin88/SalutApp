<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account;

use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\SellerRepository;

/**
 * Marketplace seller page controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class SellerController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    public function __construct(protected SellerRepository $seller)
    {
        $this->_config = request('_config');

        $this->seller = $seller;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (! core()->getConfigData('marketplace.settings.general.status')) {
            abort(404);
        }

        $seller = $this->seller->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        if (
            $seller &&
            $seller->is_approved
        ) {
            return redirect()->route('marketplace.account.seller.edit');
        }

        return view($this->_config['view'], compact('seller'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'url' => ['required', 'unique:marketplace_sellers,url', new \Webkul\Core\Contracts\Validations\Slug]
        ]);

        $data = request()->all();

        $data['customer_id'] = auth()->guard('customer')->user()->id;

        if (! core()->getConfigData('marketplace.settings.general.seller_approval_required')) {
            $data['is_approved'] = 1;

            session()->flash('success', 'Your seller account created successfully.');
        } else {
            session()->flash('success', 'Your request to become seller is successfully raised.');
        }

        $this->seller->create($data);

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $isSeller = $this->seller->isSeller(auth()->guard('customer')->user()->id);

        if (! $isSeller) {
            return redirect()->route('marketplace.account.seller.create');
        }

        $seller = $this->seller->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        return view(
            $this->_config['view'],
            compact('seller'),
            ['defaultCountry' => config('app.default_country')]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $isSeller = $this->seller->isSeller(auth()->guard('customer')->user()->id);
        $seller = $this->seller->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        if (! $isSeller) {
            return redirect()->route('marketplace.account.seller.create');
        }

        if ($id != $seller->id) {
            session()->flash('error', 'Unautorized, Something went wrong.');
            return redirect()->route($this->_config['redirect']);
        }

        $this->seller->update(request()->all(), $id);

        session()->flash('success', 'Your profile saved successfully.');

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Revoke a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function revoke()
    {
        $customerId = auth()->guard('customer')->id();

        if (! $this->seller->isSeller($customerId)) {
            $this->seller->deleteWhere([
                'customer_id' => $customerId
            ]);

            session()->flash('success', trans('marketplace::app.shop.sellers.account.profile.revoke-success'));

            return redirect()->back();
        }
    }
}
