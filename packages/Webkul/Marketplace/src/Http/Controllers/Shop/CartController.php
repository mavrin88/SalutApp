<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Webkul\Checkout\Contracts\Cart as CartModel;
use Webkul\Customer\Repositories\WishlistRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Marketplace\Repositories\ProductRepository as MarketplaceProductRepository;

class CartController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    public function __construct(
        protected WishlistRepository $wishlistRepository,
        protected ProductRepository $productRepository,
        protected MarketplaceProductRepository $mpProductRepository
    ) {
        $this->_config = request('_config');

        $this->middleware('throttle:5,1')->only('applyCoupon');

        $this->middleware('customer')->only('moveToWishlist');
    }

    /**
     * Function for guests user to add the product in the cart.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function add($id)
    {
        try {
            Cart::deactivateCurrentCartIfBuyNowIsActive();
            $result = Cart::addProduct($id, request()->all());

            if ($this->onFailureAddingToCart($result)) {
                return redirect()->back();
            }

            if ($result instanceof CartModel) {
                session()->flash('success', __('shop::app.checkout.cart.item.success'));

                if ($customer = auth()->guard('customer')->user()) {
                    if (
                        $result->items[0]->additional['seller_info'] &&
                        ! $result->items[0]->additional['seller_info']['is_owner']
                    ) {
                        $sellerWishlistProduct = $this->wishlistRepository->findWhere(['product_id' => $id, 'customer_id' => $customer->id]);
                    } else {
                        $this->wishlistRepository->deleteWhere(['product_id' => $id, 'customer_id' => $customer->id]);
                    }
                }

                if (request()->get('is_buy_now')) {
                    Event::dispatch('shop.item.buy-now', $id);

                    return redirect()->route('shop.checkout.onepage.index');
                }
            }
        } catch (\Exception $e) {
            session()->flash('warning', __($e->getMessage()));

            $product = $this->productRepository->find($id);

            Log::error(
                'Shop CartController: ' . $e->getMessage(),
                ['product_id' => $id, 'cart_id' => cart()->getCart() ?? 0]
            );

            return redirect()->route($this->_config['redirect'], $id);
        }

        return redirect()->back();
    }

    /**
     * Returns true, if result of adding product to cart
     * is an array and contains a key "warning" or "info".
     *
     * @param  array  $result
     * @return boolean
     */
    private function onFailureAddingToCart($result): bool
    {
        if (
            is_array($result) &&
            isset($result['warning'])
        ) {
            session()->flash('warning', $result['warning']);

            return true;
        }

        if (
            is_array($result) &&
            isset($result['info'])
        ) {
            session()->flash('info', $result['info']);

            return true;
        }

        return false;
    }
}
