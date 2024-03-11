<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account;

use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Carbon\Carbon;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\OrderItemRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;

/**
 * Earning controller
 *
 * @author    Mohammad Asif <mohdasif.woocommerce337@webkul.com>
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class EarningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * SellerRepository object
     *
     * @var object
     */
    protected $sellerRepository;

    /**
     * Seller object
     *
     * @var object
     */
    protected $seller;

    /**
     * OrderRepository object
     *
     * @var object
     */
    protected $orderRepository;

    /**
     * OrderItemRepository object
     *
     * @var array
     */
    protected $orderItemRepository;

    /**
     * ProductInventoryRepository object
     *
     * @var array
     */
    protected $productInventoryRepository;

    /**
     * string object
     *
     * @var array
     */
    protected $startDate;

    /**
     * string object
     *
     * @var array
     */
    protected $lastStartDate;

    /**
     * string object
     *
     * @var array
     */
    protected $endDate;

    /**
     * string object
     *
     * @var array
     */
    protected $lastEndDate;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository       $sellerRepository
     * @param  Webkul\Marketplace\Repositories\OrderRepository        $orderRepository
     * @param  Webkul\Marketplace\Repositories\OrderItemRepository    $orderItemRepository
     * @param  Webkul\Product\Repositories\ProductInventoryRepository $productInventoryRepository
     * @return void
     */
    public function __construct(
        SellerRepository $sellerRepository,
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        ProductInventoryRepository $productInventoryRepository
    ) {
        $this->_config = request('_config');

        $this->sellerRepository = $sellerRepository;

        $this->orderRepository = $orderRepository;

        $this->orderItemRepository = $orderItemRepository;

        $this->productInventoryRepository = $productInventoryRepository;
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

        $this->setStartEndDate();

        $this->seller = $this->sellerRepository->findOneByField('customer_id', auth()->guard('customer')->user()->id);

        $statistics = [];

        $dateFormat = 'd M';

        if (request()->get('period') == 'year') {
            $dateFormat = 'Y';
        } else if (request()->get('period') == 'month') {
            $dateFormat = 'M Y';
        }

        foreach ($this->getTimeInterval($this->startDate, $this->endDate) as $interval) {
            $statistics['sale_graph']['label'][] = $interval['start']->format($dateFormat);

            $query = $this->orderRepository->scopeQuery(function ($query) use ($interval) {
                return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                    ->where('marketplace_orders.created_at', '>=', $interval['start'])
                    ->where('marketplace_orders.created_at', '<=', $interval['end'])
                    ->where('seller_payout_status', '=', 'paid');
            });

            $total = $query->sum('base_seller_total_invoiced') + $query->sum('base_commission_invoiced');
            $commission = $query->sum('base_commission_invoiced');
            $discount = $query->sum('base_discount_amount_invoiced');
            $orders = $query->count();
            $totalEarning = $total - $commission;

            $statistics['sale_graph']['total'][] = $total;
            $statistics['sale_graph']['commission'][] = $commission;
            $statistics['sale_graph']['orders'][] = $orders;
            $statistics['sale_graph']['total_earning'][] = $totalEarning;
            $statistics['sale_graph']['discount'][] = $discount;

            $statistics['sale_graph']['formated_total'][] = core()->formatBasePrice($total);
        }

        return view($this->_config['view'], compact('statistics'));
    }

    /**
     * Returns time intervals
     *
     * @param \Illuminate\Support\Carbon $startDate
     * @param \Illuminate\Support\Carbon $endDate
     *
     * @return array
     */
    public function getTimeInterval($startDate, $endDate)
    {
        $timeIntervals = [];

        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalMonths = $startDate->diffInMonths($endDate) + 1;
        $totalYears = $startDate->diffInYears($endDate) + 1;

        if (request()->get('period') == 'month') {
            for ($i = 0; $i < $totalMonths; $i++) {
                $date = clone $startDate;
                $date->addMonths($i);

                if ($i === 0) {
                    $start = $startDate;
                    $end = $date->copy()->endOfMonth();
                } elseif ($i === $totalMonths - 1) {
                    $start = $date->copy()->startOfMonth();
                    $end = $endDate;
                }

                $start = Carbon::createFromTimeString($start->format('Y-m-d') . ' 00:00:01');
                $end = Carbon::createFromTimeString($end->format('Y-m-d') . ' 23:59:59');

                $timeIntervals[] = ['start' => $start, 'end' => $end, 'formatedDate' => $date->format('M')];
            }
        } else if (request()->get('period') == 'year') {
            for ($i = 0; $i < $totalYears; $i++) {
                $date = clone $startDate;
                $date->addYears($i);

                if ($i === 0) {
                    $start = $startDate;
                    $end = $date->copy()->endOfYear();
                } elseif ($i === $totalYears - 1) {
                    $start = $date->copy()->startOfYear();
                    $end = $endDate;
                }

                $start = Carbon::createFromTimeString($start->format('Y-m-d') . ' 00:00:01');
                $end = Carbon::createFromTimeString($end->format('Y-m-d') . ' 23:59:59');

                $timeIntervals[] = ['start' => $start, 'end' => $end, 'formatedDate' => $date->format('Y')];
            }
        } else {
            for ($i = 0; $i < $totalDays; $i++) {
                $date = clone $startDate;
                $date->addDays($i);

                $start = Carbon::createFromTimeString($date->format('Y-m-d') . ' 00:00:01');
                $end = Carbon::createFromTimeString($date->format('Y-m-d') . ' 23:59:59');

                $timeIntervals[] = ['start' => $start, 'end' => $end, 'formatedDate' => $date->format('d M')];
            }
        }

        return $timeIntervals;
    }

    public function getPercentageChange($previous, $current)
    {
        if (!$previous)
            return $current ? 100 : 0;

        return ($current - $previous) / $previous * 100;
    }

    /**
     * Sets start and end date
     *
     * @return void
     */
    public function setStartEndDate()
    {
        $this->startDate = request()->get('start')
            ? Carbon::createFromTimeString(request()->get('start') . " 00:00:01")
            : Carbon::createFromTimeString(Carbon::now()->subDays(30)->format('Y-m-d') . " 00:00:01");

        $this->endDate = request()->get('end')
            ? Carbon::createFromTimeString(request()->get('end') . " 23:59:59")
            : Carbon::now();

        if ($this->endDate > Carbon::now())
            $this->endDate = Carbon::now();
    }
}
