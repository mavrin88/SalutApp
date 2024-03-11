<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Marketplace\Repositories\ShipmentRepository;

/**
 * Shipment event handler
 *
 * @author    Jitendra Singh <jitendra@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Shipment
{
    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\ShipmentRepository $order
     * @return void
     */
    public function __construct(
        protected ShipmentRepository $shipment
    ) {}

    /**
     * After sales shipment creation, creater marketplace shipment
     *
     * @param mixed $shipment
     */
    public function afterShipment($shipment)
    {
        $this->shipment->create(['shipment' => $shipment]);
    }
}
