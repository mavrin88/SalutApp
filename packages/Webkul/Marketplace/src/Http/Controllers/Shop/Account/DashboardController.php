<?php

namespace Webkul\Marketplace\Http\Controllers\Shop\Account;

use Webkul\Marketplace\Http\Controllers\Shop\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\OrderRepository;
use Webkul\Marketplace\Repositories\OrderItemRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;

/**
 * Dashboard controller
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * Seller object
     *
     * @var object
     */
    protected $seller;

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
    protected $endDate;

    /**
     * string object
     *
     * @var array
     */
    protected $alpha3CountryCode;

    public function __construct(
        protected SellerRepository $sellerRepository,
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository,
        protected ProductInventoryRepository $productInventoryRepository
    ) {
        $this->_config = request('_config');

        $this->alpha3CountryCode = json_decode(file_get_contents(__DIR__ . '/../../../../Data/alpha3.json'));
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

        $currentCurrencyCode = core()->getCurrentCurrencyCode();

        $statistics = [
            'total_orders' =>  [
                'current' => $total_orders =
                    $this->orderRepository->scopeQuery(function ($query) {
                    return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                        ->where('marketplace_orders.created_at', '>=', $this->startDate)
                        ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->count()
            ],
            'total_sales' =>  [
                'current' =>
                    $this->orderRepository->scopeQuery(function ($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                        })->sum('base_seller_total_invoiced') +
                    $this->orderRepository->scopeQuery(function ($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->sum('base_commission_invoiced'),
            ],
            'total_revenue' =>  [
                'current' => $total_revenue = 
                    $this->orderRepository->scopeQuery(function ($query) {
                        return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                            ->where('marketplace_orders.created_at', '>=', $this->startDate)
                            ->where('marketplace_orders.created_at', '<=', $this->endDate);
                    })->sum('base_seller_total_invoiced')
            ],
            'avg_sales' => [
                'current' => $this->orderRepository->scopeQuery(function ($query) {
                    return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                        ->where('marketplace_orders.created_at', '>=', $this->startDate)
                        ->where('marketplace_orders.created_at', '<=', $this->endDate);
                })->sum('base_seller_total_invoiced')
            ],
            'avg_sales_count' => [
                'current' => $this->orderRepository->scopeQuery(function ($query) {
                    return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                        ->where('base_seller_total_invoiced', '!=', 0)
                        ->where('marketplace_orders.created_at', '>=', $this->startDate)
                        ->where('marketplace_orders.created_at', '<=', $this->endDate);
                })->count(),
            ],
            'top_selling_products' =>
                $this->sellerRepository->getTopSellingProducts([
                    'startDate' => $this->startDate,
                    'endDate' => $this->endDate,
                ], $this->seller->id),
            'customer_with_most_sales' =>
                $this->sellerRepository->getCustomerWithMostSales([
                    'startDate' => $this->startDate,
                    'endDate' => $this->endDate,
                ], $this->seller->id),
            'stock_threshold' =>
                $this->sellerRepository->getStockThreshold($this->seller->id),
            'seller_payout' =>
                $this->sellerRepository->getSellerPayout([
                    'startDate' => $this->startDate,
                    'endDate' => $this->endDate,
                ], $this->seller)
        ];

        foreach (core()->getTimeInterval($this->startDate, $this->endDate) as $interval) {
            $statistics['sale_graph']['label'][] = $interval['start']->format('d M');

            $total = $this->orderRepository->scopeQuery(function ($query) use ($interval) {
                return $query->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                    ->where('marketplace_orders.created_at', '>=', $interval['start'])
                    ->where('marketplace_orders.created_at', '<=', $interval['end']);
            })->sum('base_seller_total_invoiced');

            $newTotal = core()->convertPrice($total, $currentCurrencyCode);

            $statistics['sale_graph']['total'][] = $newTotal;
            $statistics['sale_graph']['formated_total'][] = core()->formatPrice($newTotal, $currentCurrencyCode);
        }

        //  map data
        $mapOrders = $this->orderRepository->scopeQuery(function ($query) {
            return $query->leftJoin('addresses', 'marketplace_orders.order_id', 'addresses.order_id')->where('marketplace_orders.marketplace_seller_id', $this->seller->id)
                ->where('address_type', 'order_shipping')
                ->where('marketplace_orders.created_at', '>=', $this->startDate)
                ->where('marketplace_orders.created_at', '<=', $this->endDate)->select(DB::raw('count(*) as seller_order_count'), DB::raw('sum(base_seller_total) as seller_total'), 'country')->groupBy('country');
        })->all();

        $data = $mapOrders->toArray();
        foreach ($data as $key => $order) {
            $data[$key]["country"]  = $this->getAlpha3Code($order["country"]);
            $data[$key]['seller_total'] = core()->formatPrice(core()->convertPrice($order["seller_total"], $currentCurrencyCode), $currentCurrencyCode);
        }
        $mapOrdersArray = json_encode($data);

        $statistics['total_revenue']['current'] = core()->convertPrice($statistics['total_revenue']['current'], $currentCurrencyCode);

        $statistics['total_sales']['current'] = core()->convertPrice($statistics['total_sales']['current'], $currentCurrencyCode);

        $statistics['avg_sales']['current'] = core()->convertPrice($statistics['avg_sales']['current'], $currentCurrencyCode);

        return view($this->_config['view'], compact('statistics', 'mapOrdersArray', 'currentCurrencyCode'));
    }

    /**
     * returns Alpha 3 country code
     */
    public function getAlpha3Code($alpha2)
    {
        foreach ($this->alpha3CountryCode as $code) {
            if ($code->alpha2 == $alpha2) {

                return $code->alpha3;
            }
        }
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

        if ($this->endDate > Carbon::now()) {
            $this->endDate = Carbon::now();
        }
    }
}
