<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Support\Facades\Event;
use Webkul\Marketplace\Repositories\SellerRepository;
use Webkul\Marketplace\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductRepository as CoreProductRepository;
use Webkul\Checkout\Facades\Cart;

/**
 * Minimum cart event handler
 *
 * @author    Mohammad Asif <mohdasif.woocommerce337@webkul.com>
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 *
 **/
class MinimumOrderController extends Controller
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
     * @var Seller
     */
    protected $sellerRepository;

    /**
     * ProductRepository object
     *
     * @var Product
     */
    protected $productRepository;

    /**
     * CoreProductRepository Object
     */
    protected $coreProductRepository;

    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\SellerRepository  $sellerRepository
     * @param  Webkul\Marketplace\Repositories\ProductRepository $productRepository
     * @return void
     */
    public function __construct(
        SellerRepository $sellerRepository,
        ProductRepository $productRepository,
        CoreProductRepository $coreProductRepository
    ) {
        $this->_config = request('_config');

        $this->sellerRepository = $sellerRepository;

        $this->productRepository = $productRepository;

        $this->coreProductRepository = $coreProductRepository;
    }

    public function index()
    {
        Event::dispatch('checkout.load.index');

        if (core()->getConfigData('marketplace.settings.minimum_order_amount.enable')) {
            if (! $this->checkCartTotal()) {
                return redirect()->back();
            }
        }

        if (
            ! auth()->guard('customer')->check()
            && ! core()->getConfigData('catalog.products.guest-checkout.allow-guest-checkout')
        ) {
            return redirect()->route('shop.customer.session.index');
        }

        if (Cart::hasError()) {
            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();

        if (
            ! auth()->guard('customer')->check() &&
            $cart->hasDownloadableItems()
        ) {
            return redirect()->route('shop.customer.session.index');
        }

        if (
            ! auth()->guard('customer')->check() &&
            ! $cart->hasGuestCheckoutItems()
        ) {
            return redirect()->route('shop.customer.session.index');
        }

        Cart::collectTotals();

        return view($this->_config['view'], compact('cart'));
    }

    /**
     * Product added to the cart
     *
     * @param mixed $cartItem
     */
    public function checkCartTotal()
    {
        $cart = Cart::getCart();

        if ($cart) {
            $sellerProductsAmount = [];

            foreach ($cart->items as $item) {
                $product = $this->productRepository->findOneWhere(['product_id' => $item->product_id, 'is_owner' => 1]);

                if ($product) {
                    $seller = $product->seller;

                    if (array_key_exists($seller->id, $sellerProductsAmount)) {
                        $sellerProductsAmount[$seller->id] += $item->total;
                    } else {
                        $sellerProductsAmount[$seller->id] = $item->total;
                    }
                }
            }

            $minAmounts = collect($sellerProductsAmount)->sort();

            foreach ($minAmounts as $key => $minAmount) {
                $seller = $this->sellerRepository->find($key);
                $minSellerAmount = ($seller->min_order_amount == "") ? 0 : $seller->min_order_amount;

                if ($minAmount < $minSellerAmount) {
                    session()->flash('warning',  __('marketplace::app.shop.minimum-order.min-order', [
                        'shop' => $seller->shop_title,
                        'amount' => core()->currency($minSellerAmount),
                    ]));

                    return false;
                }
            }

            return true;
        }
    }
}
