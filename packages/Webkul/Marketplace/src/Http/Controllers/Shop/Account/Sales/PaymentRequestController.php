<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account\Sales;

use Illuminate\Support\Facades\Mail;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Mail\PaymentRequestNotification;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\TransactionRepository;
use Webkul\User\Repositories\AdminRepository;
use Webkul\Marketplace\DataGrids\Shop\PaymentRequestDataGrid;

class PaymentRequestController extends Controller
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
     * @param  Webkul\Marketplace\Repositories\SellerRepository      $sellerRepository
     * @param  Webkul\Marketplace\Repositories\OrderRepository       $orderRepository
     * @param  Webkul\Marketplace\Repositories\TransactionRepository $transactionRepository
     * @param  Webkul\User\Repositories\AdminRepository  $adminRepository
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository,
        protected OrderRepository $orderRepository,
        protected TransactionRepository $transactionRepository,
        protected AdminRepository $adminRepository
    ) {
        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $isSeller = $this->sellerRepository->isSeller(auth()->guard('customer')->user()->id);

        if (! $isSeller) {
            return redirect()->route('marketplace.account.seller.create');
        }

        $seller = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $statistics = [
            'total_sale' =>
                $this->orderRepository->scopeQuery(function ($query) use ($seller) {
                    return $query->where('marketplace_orders.marketplace_seller_id', $seller->id);
                })->sum('base_seller_total_invoiced') +
                $this->orderRepository->scopeQuery(function ($query) use ($seller) {
                    return $query->where('marketplace_orders.marketplace_seller_id', $seller->id);
                })->sum('base_commission_invoiced'),

            'total_payout' =>
                $this->transactionRepository->scopeQuery(function ($query) use ($seller) {
                    return $query->where('marketplace_transactions.marketplace_seller_id', $seller->id);
                })->sum('base_total'),

            'remaining_payout' =>
                $this->orderRepository->scopeQuery(function ($query) use ($seller) {
                    return $query->where('marketplace_orders.marketplace_seller_id', $seller->id)
                                ->where('status', 'completed')
                                ->whereIn('seller_payout_status', ['pending', 'requested']);
                })->sum('base_seller_total'),

            'total_refunded' =>
                $this->orderRepository->scopeQuery(function ($query) use ($seller) {
                    return $query->where('marketplace_orders.marketplace_seller_id', $seller->id);
                })->sum('base_grand_total_refunded'),
        ];

        if (request()->ajax()) {
            return app(PaymentRequestDataGrid::class)->toJson();
        }

        return view($this->_config['view'], compact('statistics'));
    }

    /**
     * Update the order for payment and sends mails to admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function requestPayment($orderId)
    {
        if (Auth()->guard('customer')->user()) {
            $seller = $this->sellerRepository->findOneByField('customer_id', Auth()->guard('customer')->user()->id);

            if ($seller) {
                $mpOrder = $this->orderRepository->findOneWhere([
                    'order_id' => decrypt($orderId),
                    'marketplace_seller_id' => $seller->id
                ]);

                if (
                    $mpOrder
                    && ! in_array($mpOrder->seller_payout_status, ['paid', 'refunded', 'requested'])
                ) {
                    $mpOrder->update(['seller_payout_status' => 'requested']);

                    $admin = $this->adminRepository->findOneWhere(['role_id' => 1]);

                    try {
                        Mail::send(new PaymentRequestNotification($mpOrder, $admin));

                        session()->flash('success', trans('marketplace::app.shop.sellers.account.sales.payment-request.request-success'));
                    } catch (\Exception $e) {
                        report($e);

                        session()->flash('warning', trans('admin::app.response.something-went-wrong'));
                    }
                    
                    return redirect()->back();
                }
            }
        }        

        return abort(404);
    }
}
