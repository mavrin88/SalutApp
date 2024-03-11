<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Marketplace\Repositories\RefundRepository;

/**
 * Refund event handler
 *
 * @author    Naresh Verma <naresh.verma@webkul.com>
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class Refund
{
    /**
     * Create a new customer event listener instance.
     *
     * @param  Webkul\Marketplace\Repositories\RefundRepository $refund
     * @return void
     */
    public function __construct(
        protected RefundRepository $refund
    ) {
    }

    /**
     * After sales refund creation, create marketplace refund
     *
     * @param mixed $refund
     */
    public function afterRefund($refund)
    {
        $this->refund->create(['refund' => $refund]);
    }
}
