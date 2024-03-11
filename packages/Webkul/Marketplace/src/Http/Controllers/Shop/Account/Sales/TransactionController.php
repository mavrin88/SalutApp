<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account\Sales;

use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\TransactionRepository;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\DataGrids\Shop\TransactionDataGrid;

/**
 * Transaction controller
 *
 * @author Anmol Singh Chauhan <anmol.chauhan207@webkul.in>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class TransactionController extends Controller
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
        ];

        if (request()->ajax()) {
            return app(TransactionDataGrid::class)->toJson();
        }

        return view($this->_config['view'], compact('statistics'));
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        if (Auth()->guard('customer')->user()) {
            if ($seller = $this->sellerRepository->findOneByField('customer_id', Auth()->guard('customer')->user()->id)) {
                $transaction = $this->transactionRepository->findOneWhere([
                    'id' => $id,
                    'marketplace_seller_id' => $seller->id
                ]);

                if ($transaction) {
                    return view($this->_config['view'], compact('transaction'));
                }
            }
        }

        return abort(404);
    }
}
