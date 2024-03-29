<?php

namespace Webkul\Marketplace\Helpers;

use Webkul\Product\Helpers\Review;
use Webkul\Product\Facades\ProductImage;
use Webkul\Product\Models\Product as ProductModel;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Velocity\Repositories\OrderBrandsRepository;
use Webkul\Product\Repositories\ProductReviewRepository;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
use Webkul\Velocity\Repositories\VelocityMetadataRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Inventory\Repositories\InventorySourceRepository;
use Webkul\Marketplace\Repositories\ProductRepository as MpProduct;

class Helper extends Review
{
    /**
     * Create a new helper instance.
     *
     * @param  \Webkul\Product\Contracts\Product                        $productModel
     * @param  \Webkul\Velocity\Repositories\OrderBrandsRepository      $orderBrands
     * @param  \Webkul\Attribute\Repositories\AttributeOptionRepository $attributeOptionRepository
     * @param  \Webkul\Product\Repositories\ProductReviewRepository     $productReviewRepository
     * @param  \Webkul\Velocity\Repositories\VelocityMetadataRepository $velocityMetadataRepository
     * @return void
     */
    public function __construct(
        protected ProductModel $productModel,
        protected ProductRepository $productRepository,
        protected AttributeOptionRepository $attributeOptionRepository,
        protected ProductFlatRepository $productFlatRepository,
        protected OrderBrandsRepository $orderBrandsRepository,
        protected ProductReviewRepository $productReviewRepository,
        protected VelocityMetadataRepository $velocityMetadataRepository,
        protected ProductInventoryRepository $productInventoryRepository,
        protected MpProduct $mpProduct
    ) {
    }

    /**
     * @param  \Webkul\Product\Contracts\Product  $product
     * @param  bool                               $list
     * @param  array                              $metaInformation
     *
     * @return array
     */
    public function formatProduct($product, $list = false, $metaInformation = [])
    {
        $reviewHelper = app('Webkul\Product\Helpers\Review');

        $galleryImages = ProductImage::getGalleryImages($product);
        $productImage = ProductImage::getProductBaseImage($product, $galleryImages)['medium_image_url'];

        $largeProductImageName = "large-product-placeholder.png";
        $mediumProductImageName = "meduim-product-placeholder.png";

        if (strpos($productImage, $mediumProductImageName) > -1) {
            $productImageNameCollection = explode('/', $productImage);
            $productImageName = $productImageNameCollection[sizeof($productImageNameCollection) - 1];

            if ($productImageName == $mediumProductImageName) {
                $productImage = str_replace($mediumProductImageName, $largeProductImageName, $productImage);
            }
        }

        $priceHTML = view('shop::products.price', ['product' => $product])->render();

        $isProductNew = ($product->new && !strpos($priceHTML, 'sticker sale') > 0) ? __('shop::app.products.new') : false;

        return [
            'priceHTML'         => $priceHTML,
            'avgRating'         => ceil($reviewHelper->getAverageRating($product)),
            'totalReviews'      => $reviewHelper->getTotalReviews($product),
            'image'             => $productImage,
            'new'               => $isProductNew,
            'galleryImages'     => $galleryImages,
            'name'              => $product->name,
            'slug'              => $product->url_key,
            'description'       => $product->description,
            'shortDescription'  => $product->short_description,
            'firstReviewText'   => trans('velocity::app.products.be-first-review'),
            'addToCartHtml'     => view('shop::products.add-to-cart', [
                'product'           => $product,
                'addWishlistClass'  => !(isset($list) && $list) ? '' : '',

                'showCompare'       => core()->getConfigData('general.content.shop.compare_option') == "1"
                    ? true : false,

                'btnText'           => (isset($metaInformation['btnText']) && $metaInformation['btnText'])
                    ? $metaInformation['btnText'] : null,

                'moveToCart'        => (isset($metaInformation['moveToCart']) && $metaInformation['moveToCart'])
                    ? $metaInformation['moveToCart'] : null,

                'addToCartBtnClass' => !(isset($list) && $list) ? 'small-padding' : '',
            ])->render(),
        ];
    }

    /**
     * @param  object $product
     *
     * @return boolean
     */
    public function isSaleable($product)
    {
        if (
            isset($product->variants) &&
            count($product->variants) > 0
        ) {
            foreach ($product->variants as $variant) {
                if ($this->haveSufficientQuantity(1, $variant)) {
                    return true;
                }
            }
        } else {
            if (!$product->status) {
                return false;
            }

            if (
                is_callable(config('products.isSaleable')) &&
                call_user_func(config('products.isSaleable'), $product) === false
            ) {
                return false;
            }

            if ($this->haveSufficientQuantity(1, $product)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  int  $qty
     * @return bool
     */
    public function haveSufficientQuantity(int $qty, $product): bool
    {
        $backorders = core()->getConfigData('catalog.inventory.stock_options.backorders');

        $backorders = !is_null($backorders) ? $backorders : false;

        return $qty <= $product->totalQuantity() ? true : $backorders;
    }

    /**
     * @param  object  $product
     * @return bool
     */
    public function stockHaveSufficientQuantity($product)
    {
        $backorders = core()->getConfigData('catalog.inventory.stock_options.backorders');

        $backorders = !is_null($backorders) ? $backorders : false;

        if (
            isset($product->variants) &&
            count($product->variants)
        ) {
            foreach ($product->variants as $variant) {
                if ($this->haveSufficientQuantity(1, $variant)) {
                    return true;
                }
            }
        } else {
            if ($this->haveSufficientQuantity(1, $product)) {
                return true;
            }
        }

        return $backorders;
    }
}
