<?php

namespace Webkul\MarketplaceWarehouse\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * Seller Warehouse Repository
 *
 */
class WarehouseRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\MarketplaceWarehouse\Contracts\Warehouse';
    }
}