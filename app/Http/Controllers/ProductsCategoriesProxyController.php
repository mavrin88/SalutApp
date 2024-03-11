<?php

namespace App\Http\Controllers;

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
     * @param \Webkul\Category\Repositories\CategoryRepository $categoryRepository
     * @param \Webkul\Product\Repositories\ProductRepository $productRepository
     * @param \Webkul\Marketplace\Repositories\MpProductRepository $mpProductRepository
     * @return void
     */
    public function __construct(
        protected CategoryRepository  $categoryRepository,
        protected ProductRepository   $productRepository,
        protected SliderRepository    $sliderRepository,
        protected MpProductRepository $mpProductRepository
    )
    {
        $this->_config = request('_config');
    }

    /**
     * Show root category items.
     *
     * @param CategoryRepository $categoryRepository
     * @return \Illuminate\View\View
     */
    public function all(CategoryRepository $categoryRepository): \Illuminate\View\View
    {
        $rootCategoryId = core()->getCurrentChannel()->root_category_id;

        $rootCategory = $categoryRepository->find($rootCategoryId);
        $categories = $categoryRepository->getChildCategories($rootCategoryId);

        return view('shop::category.index', compact('rootCategory','categories'));
    }
}
