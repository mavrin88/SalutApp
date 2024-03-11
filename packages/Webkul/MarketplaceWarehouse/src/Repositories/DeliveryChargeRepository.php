<?php

namespace Webkul\MarketplaceWarehouse\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * Delivery Charge Repository
 *
 */
class DeliveryChargeRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\MarketplaceWarehouse\Contracts\DeliveryCharge';
    }
}