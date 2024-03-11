<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\TransactionRepository;
use Webkul\Marketplace\DataGrids\Admin\PaymentRequestDataGrid;

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
     * @return void
     */
    public function __construct(
        protected SellerRepository $sellerRepository,
        protected OrderRepository $orderRepository,
        protected TransactionRepository $transactionRepository
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
        if (request()->ajax()) {
            return app(PaymentRequestDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Update the order for payment and sends mails to admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function requestPayment($orderId)
    {
        $orderRepository = $this->orderRepository->findOneWhere(['order_id' => $orderId]);

        if ($orderRepository) {

            $orderRepository->update(['seller_payout_status' => 'requested']);

            session()->flash('success', 'Payment has been requested');
        }

        return redirect()->back();
    }
}
