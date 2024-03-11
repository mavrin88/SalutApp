<?php

namespace Webkul\MarketplaceWarehouse\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * Warehouse Assign City Repository
 *
 */
class AssignCityRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\MarketplaceWarehouse\Contracts\AssignCity';
    }
}