<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductFlatRepository;

class CategoryController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    public function __construct(
        protected AttributeRepository $attributeRepository,
        protected CategoryRepository $categoryRepository,
        protected ProductFlatRepository $productFlatRepository
    ) {
        $this->_config = request('_config');
    }

    /**
     * Get filter attributes for product.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFilterAttributes($categoryId = null, AttributeRepository $attributeRepository)
    {
        $category = $this->categoryRepository->findOrFail($categoryId);

        if (empty($filterAttributes = $category->filterableAttributes)) {
            $filterAttributes = $attributeRepository->getFilterAttributes();
        }

        return response()->json([
            'filter_attributes' => $filterAttributes,
        ]);
    }

    /**
     * Get category product maximum price.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategoryProductMaximumPrice($categoryId)
    {
        $category = $this->categoryRepository->findOrFail($categoryId);

        $maxPrice = $this->productFlatRepository->handleCategoryProductMaximumPrice($category);

        return response()->json([
            'max_price' => $maxPrice,
        ]);
    }
}
