<?php

namespace Webkul\MarketplaceWarehouse\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * Warehouse Delivery Type Repository
 *
 */
class DeliveryTypeRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\MarketplaceWarehouse\Contracts\DeliveryType';
    }
}