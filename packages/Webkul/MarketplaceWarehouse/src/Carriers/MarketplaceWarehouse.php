<?php

namespace Webkul\MarketplaceWarehouse\Carriers;

use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Shipping\Carriers\AbstractShipping;

/**
 * Marketplace Table Rate Shipping.
 *
 */
class MarketplaceWarehouse extends AbstractShipping
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'mpwarehouse';

    /**
     * Returns rate for flatrate
     *
     * @return array
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        $shippingMethods = [];

        $shippingData = app('Webkul\MarketplaceWarehouse\Helpers\ShippingHelper');

        $shippingDetails = $shippingData->findAppropriateTableRateMethods();
      
        $allMethods = $shippingData->getMethodWiseShippingData($shippingDetails);

        $weightDetails = $shippingData->findWeight();

        if ($weightDetails != null) {
            $sellerWeights = [];

            foreach ($weightDetails as $sellerProducts) {
                foreach ($sellerProducts as $product) {
                    // Extract seller ID, weight, and warehouse_region_id from the product data.
                    $sellerId = $product['marketplace_seller_id'];
                    $weight = (float) $product['weight'] * $product['quantity'];
                    $warehouseRegionId = $product['warehouse_region_id'];
            
                    // Check if the sellerId exists in the sellerWeights array.
                    if (array_key_exists($sellerId, $sellerWeights)) {
                        // If it exists, add the weight to the existing total weight.
                        $sellerWeights[$sellerId]['total_weight'] += $weight;
                    } else {
                        // If it doesn't exist, initialize the total weight for the seller.
                        $sellerWeights[$sellerId] = [
                            'total_weight' => $weight,
                            'warehouse_region_id' => $warehouseRegionId,
                        ];
                    }
                }
            }
            
            // Now $sellerWeights contains the total weight and warehouse_region_id for each seller.
            foreach ($sellerWeights as $sellerId => $data) {
                $totalWeight = $data['total_weight'];
                $warehouseRegionId = $data['warehouse_region_id'];
               
                $regionWeight = app('Webkul\MarketplaceWarehouse\Repositories\RegionRepository')->find($warehouseRegionId)->max_weight;

                if ($totalWeight > $regionWeight) {
                    session()->flash('error', trans('marketplace_warehouse::app.shop.warehouse.region.update-failed'));
                
                    return $shippingMethods;
                }
            }            
        }

        $object = new CartShippingRate;

        $object->carrier = 'mpwarehouse';

        $object->carrier_title = $this->getConfigData('title');

        $object->method = $this->getMethod();

        $object->method_title = $this->getConfigData('title');

        $object->method_description = $this->getConfigData('description');

        $object->price = 0;

        $object->base_price = 0;

        if ($allMethods !== null) {
            $totalShippingCost = array_reduce($allMethods[0], function ($carry, $item) {
                if ($this->getConfigData('type') == 'per_unit')
                    return $carry + ($item['shipping_cost'] * $item['quantity']);
                else
                    return $carry + $item['shipping_cost'];
            }, 0);

            $object->price = $totalShippingCost;

            $object->base_price = $totalShippingCost;
    
            array_push($shippingMethods, $object);
    
            return $shippingMethods;
        }

        array_push($shippingMethods, $object);
        return $shippingMethods;
    }
}