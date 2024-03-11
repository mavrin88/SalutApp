<?php

namespace Webkul\MarketplaceWarehouse\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;
use Webkul\MarketplaceWarehouse\Repositories\ReceiptRepository;
use Webkul\MarketplaceWarehouse\Repositories\QtyLogRepository;
use Webkul\MarketplaceWarehouse\Repositories\PriceRepository;
use Webkul\Product\Repositories\ProductFlatRepository;

/**
 * Warehouse Discount Repository
 *
 */
class DiscountRepository extends Repository
{
   /**
     * Create a new repository instance.
     *
     * @param  \Webkul\MarketplaceWarehouse\Repositories\ReceiptRepository $receipt
     * @param  \Webkul\MarketplaceWarehouse\Repositories\QtyLogRepository $qtyLog
     * @param  \Webkul\MarketplaceWarehouse\Repositories\PriceRepository $price
     * @param  \Webkul\Product\Repositories\ProductFlatRepository $product
     * @param  \Illuminate\Container\Container  $container
     * @return void
     */
    public function __construct(
        protected ReceiptRepository $receipt,
        protected QtyLogRepository $qtyLog,
        protected PriceRepository $price,
        protected ProductFlatRepository $product,
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
        return 'Webkul\MarketplaceWarehouse\Contracts\Discount';
    }

    /**
     * Create product.
     *
     * @param  object $data
     */
    public function create($data)
    {
        $regionId = $data->id;

        $receiptIds = $this->receipt->findByField('warehouse_id', $data->warehouse_id)->toArray();

        foreach ($receiptIds as $receiptId) {
            $productIds = $this->qtyLog->where('receipt_id', $receiptId)->pluck('associated_product_id')->toArray();

            foreach ($productIds as $productId) {

                $warehousePriceData = $this->price->findOneWhere([
                    'price_type_id' => $data->price_type_id,
                    'product_id'    => $productId
                ]);

                $warehousePrice = $warehousePriceData ? $warehousePriceData->price : 0;

                $productData = parent::where('warehouse_region_id', $regionId)
                                     ->where('product_id', $productId)
                                     ->first();

                if (! $productData) {
                    parent::create([
                        'product_id'            => $productId,
                        'discount'              => 0,
                        'warehouse_region_id'   => $regionId,
                        'real_selling_price'    => $warehousePrice,
                        'base_selling_price'    => $warehousePrice,
                    ]);
                }
            }
        }
    }


    /**
     * Create product.
     *
     * @param  object $data
     */
    public function updateData($data)
    {
//        dd($data);
        $regionId = $data->id;

        $receiptIds = $this->receipt->findByField('warehouse_id', $data->warehouse_id)->toArray();

        foreach ($receiptIds as $receiptId) {
            $productIds = $this->qtyLog->where('receipt_id', $receiptId)->pluck('associated_product_id')->toArray();

            foreach ($productIds as $productId) {

                $warehousePriceData = $this->price->findOneWhere([
                    'price_type_id' => $data->price_type_id,
                    'product_id'    => $productId
                ]);

                $warehousePrice = $warehousePriceData ? $warehousePriceData->price : 0;

                $productData = parent::where('warehouse_region_id', $regionId)
                                     ->where('product_id', $productId)
                                     ->first();

                if (! $productData) {
                    parent::create([
                        'product_id'            => $productId,
                        'discount'              => 0,
                        'warehouse_region_id'   => $regionId,
                        'real_selling_price'    => $warehousePrice,
                        'base_selling_price'    => $warehousePrice,
                    ]);
                } else {
                    parent::update([
                        'discount'              => $productData->discount,
                        'real_selling_price'    => $productData->real_selling_price,
                        'base_selling_price'    => $productData->base_selling_price,
                    ], $productData->id);
                }
            }
        }
    }
}
