<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Webkul\Core\Repositories\SliderRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Marketplace\Repositories\ProductRepository as MpProductRepository;

class ProductsCategoriesProxyController extends Controller
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
     * @param  \Webkul\Category\Repositories\CategoryRepository  $categoryRepository
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Marketplace\Repositories\MpProductRepository  $mpProductRepository
     * @return void
     */
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository,
        protected SliderRepository $sliderRepository,
        protected MpProductRepository $mpProductRepository
    ) {
        $this->_config = request('_config');
    }

    /**
     * Show product or category view. If neither category nor product matches, abort with code 404.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Exception
     */
    public function index(Request $request)
    {
        $slugOrPath = trim($request->getPathInfo(), '/');

        $slugOrPath = urldecode($slugOrPath);

        // support url for chinese, japanese, arbic and english with numbers.
        if (preg_match('/^([\x{0621}-\x{064A}\x{4e00}-\x{9fa5}\x{3402}-\x{FA6D}\x{3041}-\x{30A0}\x{30A0}-\x{31FF}_a-z0-9-]+\/?)+$/u', $slugOrPath)) {
            if ($category = $this->categoryRepository->findByPath($slugOrPath)) {
                $childCategory = $this->categoryRepository->getChildCategories($category->id);

                return view($this->_config['category_view'], compact('category', 'childCategory'));
            }

            if ($product = $this->productRepository->findBySlug($slugOrPath)) {

                $sellerProduct = $this->mpProductRepository->findOneByField('product_id', $product->product->id);

                if (
                    $sellerProduct
                    && $sellerProduct->is_owner == 1
                    && ! $sellerProduct->is_approved
                ) {
                    session()->flash('warning', __('Seller product #' . $product->product->id . ' should be approved before making product status active'));

                    return redirect()->back();
                }

                $customer = auth()->guard('customer')->user();
                $seller = app('Webkul\Marketplace\Repositories\ProductRepository')->getSellerByProductId($product->id);

                if (
                    (
                        $product->status &&
                        $product->url_key &&
                        $product->visible_individually &&
                        $seller
                    )
                    || ! $seller
                    || strpos(url()->previous(), "/admin/")
                ) {
                    return view($this->_config['product_view'], [
                        'product'          => $product,
                        'customer'         => auth()->guard('customer')->user(),
                        'relatedProducts'  => $product->related_products()->take(core()->getConfigData('catalog.products.product_view_page.no_of_related_products'))->get(),
                        'upSellProducts'  => $product->up_sells()->take(core()->getConfigData('catalog.products.product_view_page.no_of_up_sells_products'))->get(),
                    ]);
                }
            }

            abort(404);
        }

        $sliderData = $this->sliderRepository->getActiveSliders();

        return view('shop::home.index', compact('sliderData'));
    }
}
