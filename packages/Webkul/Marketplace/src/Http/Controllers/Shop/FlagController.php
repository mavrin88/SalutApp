<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Mail\ReportProductNotification;
use Webkul\Marketplace\Mail\AdminReportProductNotification;
use Webkul\Marketplace\Mail\ReportSellerNotification;
use Webkul\Marketplace\Mail\AdminReportSellerNotification;
use Webkul\Marketplace\Repositories\ProductFlagRepository;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Marketplace\Repositories\SellerFlagRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\User\Repositories\AdminRepository;

/**
 * Marketplace flag controller
 *
 * @author    Anmol Singh Chauhan <anmol.chauhan207@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class FlagController extends Controller
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
     * @param  Webkul\Marketplace\Repositories\ProductFlagRepository $productFlagRepository
     * @param  Webkul\Marketplace\Repositories\SellerFlagRepository $sellerFlagRepository
     * @param  Webkul\User\Repositories\AdminRepository  $adminRepository
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository,
        protected SellerFlagRepository $sellerFlagRepository,
        protected ProductFlagRepository $productFlagRepository,
        protected ProductRepository $productRepository,
        protected AdminRepository $adminRepository
    ) {
        $this->_config = request('_config');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function productFlagstore()
    {
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required',
            'product_id' => 'required'
        ]);

        $flag = $this->productFlagRepository->findOneByField(['email' => request()->email, 'product_id' => request()->product_id]);

        if (! $flag) {
            $data = request()->all();

            $seller = $this->sellerRepository->find(request()->seller_id);

            $data['admin'] = $this->adminRepository->findOneWhere(['role_id' => 1]);

            $data['product'] = $this->productRepository->findOneByField('product_id', request()->product_id)->product;

            $this->productFlagRepository->create($data);

            $data['subject'] = 'Report Seller Product';

            try {
                Mail::send(new ReportProductNotification($seller->customer, $data));
                Mail::send(new AdminReportProductNotification($seller->customer, $data));

                session()->flash('success', __('marketplace::app.shop.flag.success-msg'));
            } catch (\Exception $e) {
                report($e);
                session()->flash('warning', __('marketplace::app.shop.flag.error-msg'));
            }
        } else {
            session()->flash('warning', __('marketplace::app.shop.flag.report-msg'));
        }

        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function sellerFlagstore()
    {
        try {
            $this->validate(request(), [
                'name' => 'required',
                'reason' => 'required',
                'email' => 'required|unique:marketplace_seller_flags'
            ]);

            $data = request()->all();
            $data['query'] = $data['reason'];

            $seller = $this->sellerRepository->find($data['seller_id']);

            if (
                ! empty(auth()->guard('customer')->user()->id) &&
                ! empty($this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id)->id)
            ) {
                $sellerId = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id)->id;

                if ($sellerId == $seller->id) {
                    session()->flash('warning',  __('marketplace::app.shop.flag.self-report-err'));

                    return redirect()->back();
                } else {
                    $this->sellerFlagRepository->create($data);

                    session()->flash('success', __('marketplace::app.shop.flag.seller-report'));
                }
            } else {
                $this->sellerFlagRepository->create($data);
                
                session()->flash('success', __('marketplace::app.shop.flag.seller-report'));
            }

            Mail::send(new ReportSellerNotification($seller, $data));

            Mail::send(new AdminReportSellerNotification($seller, $data));
        } catch (\Exception $e) {
            report($e);
            session()->flash('warning', $e->getMessage());
        }

        return redirect()->back();
    }
}
