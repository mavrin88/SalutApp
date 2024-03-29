<?php

namespace Webkul\Product\Type;

use Illuminate\Support\Facades\DB;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductImageRepository;
use Webkul\Product\Repositories\ProductVideoRepository;
use Webkul\Product\Repositories\ProductCustomerGroupPriceRepository;
use Webkul\Tax\Repositories\TaxCategoryRepository;
use Webkul\Product\Repositories\ProductGroupedProductRepository;
use Webkul\Product\Helpers\Indexers\Price\Grouped as GroupedIndexer;

class Grouped extends AbstractType
{
    /**
     * Skip attribute for downloadable product type.
     *
     * @var array
     */
    protected $skipAttributes = [
        'price',
        'cost',
        'special_price',
        'special_price_from',
        'special_price_to',
        'length',
        'width',
        'height',
        'weight',
        'depth',
    ];

    /**
     * These blade files will be included in product edit page.
     *
     * @var array
     */
    protected $additionalViews = [
        'admin::catalog.products.accordians.images',
        'admin::catalog.products.accordians.videos',
        'admin::catalog.products.accordians.categories',
        'admin::catalog.products.accordians.grouped-products',
        'admin::catalog.products.accordians.channels',
        'admin::catalog.products.accordians.product-links',
    ];

    /**
     * Is a composite product type.
     *
     * @var boolean
     */
    protected $isComposite = true;

    /**
     * Create a new product type instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @param  \Webkul\Attribute\Repositories\AttributeRepository  $attributeRepository
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Product\Repositories\ProductAttributeValueRepository  $attributeValueRepository
     * @param  \Webkul\Product\Repositories\ProductInventoryRepository  $productInventoryRepository
     * @param  \Webkul\Product\Repositories\ProductImageRepository  $productImageRepository
     * @param  \Webkul\Product\Repositories\ProductCustomerGroupPriceRepository  $productCustomerGroupPriceRepository
     * @param  \Webkul\Tax\Repositories\TaxCategoryRepository  $taxCategoryRepository
     * @param  \Webkul\Product\Repositories\ProductGroupedProductRepository  $productGroupedProductRepository
     * @param  \Webkul\Product\Repositories\ProductVideoRepository  $productVideoRepository
     * @return void
     */
    public function __construct(
        CustomerRepository $customerRepository,
        AttributeRepository $attributeRepository,
        ProductRepository $productRepository,
        ProductAttributeValueRepository $attributeValueRepository,
        ProductInventoryRepository $productInventoryRepository,
        ProductImageRepository $productImageRepository,
        ProductVideoRepository $productVideoRepository,
        ProductCustomerGroupPriceRepository $productCustomerGroupPriceRepository,
        TaxCategoryRepository $taxCategoryRepository,
        protected ProductGroupedProductRepository $productGroupedProductRepository
    ) {
        parent::__construct(
            $customerRepository,
            $attributeRepository,
            $productRepository,
            $attributeValueRepository,
            $productInventoryRepository,
            $productImageRepository,
            $productVideoRepository,
            $productCustomerGroupPriceRepository,
            $taxCategoryRepository
        );
    }

    /**
     * Update.
     *
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Product\Contracts\Product
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        $product = parent::update($data, $id, $attribute);

        if (request()->route()?->getName() == 'admin.catalog.products.mass_update') {
            return $product;
        }

        $this->productGroupedProductRepository->saveGroupedProducts($data, $product);

        return $product;
    }

    /**
     * Copy relationships.
     *
     * @param  \Webkul\Product\Models\Product  $product
     * @return void
     */
    protected function copyRelationships($product)
    {
        parent::copyRelationships($product);

        $attributesToSkip = config('products.skipAttributesOnCopy') ?? [];

        if (in_array('grouped_products', $attributesToSkip)) {
            return;
        }

        foreach ($this->product->grouped_products as $groupedProduct) {
            $product->grouped_products()->save($groupedProduct->replicate());
        }
    }

    /**
     * Returns children ids.
     *
     * @return array
     */
    public function getChildrenIds()
    {
        return array_unique($this->product->grouped_products()->pluck('associated_product_id')->toArray());
    }

    /**
     * Check if catalog rule can be applied.
     *
     * @return bool
     */
    public function priceRuleCanBeApplied()
    {
        return false;
    }

    /**
     * Is saleable.
     *
     * @return bool
     */
    public function isSaleable()
    {
        if (!$this->product->status) {
            return false;
        }

        foreach ($this->product->grouped_products as $groupedProduct) {
            if ($groupedProduct->associated_product->isSaleable()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Is product have sufficient quantity.
     *
     * @param  int  $qty
     * @return bool
     */
    public function haveSufficientQuantity(int $qty): bool
    {
        foreach ($this->product->grouped_products as $groupedProduct) {
            if ($groupedProduct->associated_product->haveSufficientQuantity($qty)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get product minimal price.
     *
     * @return string
     */
    public function getPriceHtml()
    {
        $html = '';

        if ($this->haveDiscount()) {
            $html .= '<div class="sticker sale">' . trans('shop::app.products.sale') . '</div>';
        }

        $html .= '<span class="price-label">' . trans('shop::app.products.starting-at') . '</span>'
            . ' '
            . '<span class="final-price">' . core()->currency($this->getMinimalPrice()) . '</span>';

        return $html;
    }

    /**
     * Add product. Returns error message if can't prepare product.
     *
     * @param  array  $data
     * @return array
     */
    public function prepareForCart($data)
    {
        if (
            !isset($data['qty'])
            || !is_array($data['qty'])
        ) {
            return trans('shop::app.checkout.cart.integrity.missing_options');
        }

        $cartProductsList = [];

        foreach ($data['qty'] as $productId => $qty) {
            if (!$qty) {
                continue;
            }

            $additionalAttriubtes = [
                'product_id' => $productId,
                'quantity'   => $qty,
            ];

            if (!empty($data['seller_info'])) {
                $additionalAttriubtes['seller_info'] = [
                    'product_id' => $productId,
                    'seller_id' => $data['seller_info']['seller_id'],
                    'is_owner'  => $data['seller_info']['is_owner']
                ];
                if (empty($data['seller_info']['is_owner'])) {
                    $additionalAttriubtes['product_type'] = 'grouped_product_type';
                    $additionalAttriubtes['grouped_product_id'] = $data['product_id'];
                    $additionalAttriubtes['seller_id'] = $data['seller_info']['seller_id'];
                }
            }

            $product = $this->productRepository->find($productId);

            $cartProducts = $product->getTypeInstance()->prepareForCart($additionalAttriubtes);
            $cartProducts = $this->sellerPriceOfGroupedProduct($cartProducts, $data);

            if (is_string($cartProducts)) {
                return $cartProducts;
            }

            $cartProductsList[] = $cartProducts;
        }

        $products = array_merge(...$cartProductsList);

        if (!count($products)) {
            return trans('shop::app.checkout.cart.integrity.qty_missing');
        }

        return $products;
    }

    /*
       This method is used to replace price of product with seller grouped products price,
       When seller sell admin grouped product with own price.
    */
    public function sellerPriceOfGroupedProduct($cartItem, $requestData)
    {
        if (is_array($cartItem)) {
            if (!empty($requestData['seller_info'])) {
                if (empty($requestData['seller_info']['is_owner'])) {

                    $cartItemCollection = collect($cartItem)->map(function ($item, $key) {

                        $mainGroupedProductId = $item['additional']['grouped_product_id'];
                        $associatedProductId =  $item['product_id'];
                        $sellerId = $item['additional']['seller_id'];

                        /*Find associated products group details*/
                        $productGroupedDetail = DB::table('product_grouped_products')->where([['product_id', '=', $mainGroupedProductId], ['associated_product_id', '=', $associatedProductId]])->first();

                        if ($productGroupedDetail != NULL) {
                            $sellerPriceofAssociatesProduct =  DB::table('mp_grouped_product_price')->where([
                                ['product_grouped_product_id', '=', $productGroupedDetail->id],
                                ['marketplace_seller_id', '=', $sellerId]
                            ])->first();

                            if ($sellerPriceofAssociatesProduct != NULL) {
                                $item['price']        = $sellerPriceofAssociatesProduct->seller_sell_price;
                                $item['base_price']   = $sellerPriceofAssociatesProduct->seller_sell_price;
                                $item['total']        = ($sellerPriceofAssociatesProduct->seller_sell_price * $item['quantity']);
                                $item['base_total'] = ($sellerPriceofAssociatesProduct->seller_sell_price * $item['quantity']);

                                return $item;
                            } else {
                                return $item;
                            }
                        } else {
                            return $item;
                        }
                        return $item;
                    })->all();
                    return $cartItemCollection;
                }
                return $cartItem;
            }
            return $cartItem;
        }
        return $cartItem;
    }

    /**
     * Returns price indexer class for a specific product type
     *
     * @return string
     */
    public function getPriceIndexer()
    {
        return app(GroupedIndexer::class);
    }
}
