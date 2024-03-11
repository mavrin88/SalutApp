<?php

namespace Webkul\MarketplaceWarehouse\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Repositories\ProductRepository as SellerProduct;
use Webkul\Product\Repositories\ProductRepository as CoreProduct;

/**
 * Seller Warehouse Repository
 *
 */
class ReceiptRepository extends Repository
{
    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Marketplace\Repositories\ProductRepository $sellerProduct
     * @param  \Webkul\Product\Repositories\ProductRepository $coreProduct
     * @param  \Illuminate\Container\Container $container;
     * @return void
     */
    public function __construct(
        protected SellerProduct $sellerProduct,
        protected CoreProduct $coreProduct,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\MarketplaceWarehouse\Contracts\Receipt';
    }

    /**
     * Search simple products.
     *
     * @return mixed
     */
    function getSimpleProducts($id)
    {
        $sellerProductIds = $this->sellerProduct->findWhere([
//            'is_owner' => 1,
            'is_approved' => 1,
            'marketplace_seller_id' => $id
        ])->pluck('product_id');

        $productType = ['simple'];

        $products = $this->coreProduct
            ->with('product_flats')
            ->with('inventory_indices')
            ->whereHas('product_flats', function ($query) use ($productType) {
                $query->where('status', 1)
                ->whereIn('type', $productType)
                ->where('name', 'like', '%' . request()->input('query') . '%');
            })
            ->whereIn('id', $sellerProductIds)
            ->orderBy('id', 'desc')
            ->limit(50)
            ->paginate();

        return response()->json($products);
    }
}
