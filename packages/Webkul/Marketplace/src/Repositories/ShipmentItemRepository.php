<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * Seller Shipment Reposotory
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ShipmentItemRepository extends Repository
{
    protected $guarded = ['id', 'child', 'created_at', 'updated_at'];
    
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Marketplace\Contracts\ShipmentItem';
    }
}