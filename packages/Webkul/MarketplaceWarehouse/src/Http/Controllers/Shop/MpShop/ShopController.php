<?php

namespace Webkul\MarketplaceWarehouse\Http\Controllers\Shop\MpShop;

use Webkul\Marketplace\Http\Controllers\Shop\ShopController as Mpshop;
use Webkul\Product\Facades\ProductImage;

class ShopController extends Mpshop
{
    /**
     * Fetch product details.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function fetchProductDetails($slug)
    {
        $status = core()->getConfigData('marketplace.settings.general.status');

        if ($status) {
            $adminAllowCity = core()->getConfigData('sales.shipping.origin.city');

            $city = str_replace(' ', '+', session()->get('location'));

            $isAdminAllowCity = ! empty($city) && (strtolower($city) == strtolower($adminAllowCity));

            $sellers = app('Webkul\MarketplaceWarehouse\Helpers\Location')->getAllowedSellers();

            $isSellerAllowCity = empty($sellers) ? [] : array_column($sellers->toArray(), 'id');

            $productIds = [];

            $sellerProductIds = [];

            if ($isAdminAllowCity) {
                $productIds = app('Webkul\MarketplaceWarehouse\Repositories\MpProduct\ProductRepository')->getSerachProductIds($isSellerAllowCity);
            } else {
                $sellerProductIds = app('Webkul\MarketplaceWarehouse\Repositories\MpProduct\ProductRepository')->getSerachProductIds($isSellerAllowCity, true);
            }
        }

        $product = $this->productRepository->findBySlug($slug);

        if ($product?->status) {
            if (($isAdminAllowCity && ! in_array($product->id, $productIds)) || (in_array($product->id, $sellerProductIds))) {
                $productReviewHelper = app('Webkul\Product\Helpers\Review');

                $galleryImages = ProductImage::getProductBaseImage($product);

                $response = [
                    'status'  => true,
                    'details' => [
                        'name'         => $product->name,
                        'urlKey'       => $product->url_key,
                        'priceHTML'    => view('shop::products.price', ['product' => $product])->render(),
                        'totalReviews' => $productReviewHelper->getTotalReviews($product),
                        'rating'       => ceil($productReviewHelper->getAverageRating($product)),
                        'image'        => $galleryImages['small_image_url'],
                    ],
                ];
            } else {
                    $response = [
                        'status' => false,
                        'slug'   => $slug,
                    ];
            }
        } else {
            $response = [
                'status' => false,
                'slug'   => $slug,
            ];
        }

        return $response;
    }
}
