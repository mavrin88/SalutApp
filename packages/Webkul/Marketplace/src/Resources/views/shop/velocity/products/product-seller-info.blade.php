@push('css')
    <style>
        .product-detail .seller-info {
            margin-bottom: 15px;
        }

        .seller-info .star-blue-icon {
            vertical-align: text-top;
        }
    </style>
@endpush

@php
    $productRepository = app('Webkul\Marketplace\Repositories\ProductRepository');
    $sellerRepository = app('Webkul\Marketplace\Repositories\SellerRepository');
    $reviewRepository = app('Webkul\Marketplace\Repositories\ReviewRepository');

    if (! empty($item->additional['seller_info'])) {
        $seller = $sellerRepository->find($item->additional['seller_info']['seller_id']);
    } else {
        $seller = $productRepository->getSellerByProductId($product->product_id);
    }
@endphp

@if ($seller && $seller->is_approved)

    @php
        $sellerProduct = $productRepository->getMarketplaceProductByProduct($product->product_id, $seller->id);
    @endphp

    @if (
         (
            $sellerProduct->is_approved
            && $sellerProduct->is_owner
        ) || ! empty($item->additional['seller_info'])
    )

        <div class="seller-info" style="padding-bottom:15px;">

            {!!
                __('marketplace::app.shop.products.sold-by', [
                        'url' => "<a href=" . route('marketplace.seller.show', $seller->url) . ">" . $seller->shop_title . " [<i class='icon star-blue-icon'></i>" . $reviewRepository->getAverageRating($seller) . "]</a>"
                    ])
            !!}

        </div>

    @endif

@endif
