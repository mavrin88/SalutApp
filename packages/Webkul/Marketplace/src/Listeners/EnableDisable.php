<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Marketplace\Models\SellerProductType;

/**
 * Product event handler
 *
 * @author    Anmol Singh Chauhan <anmol.chauhan207@webkul.com>
 * @copyright 2022 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class EnableDisable
{
    public function manageConfig()
    {
        if (! empty(request('marketplace.products-setting'))) {
            $configData = [];
            $productSetting = '';
            $allowProductType = '';
            $productType = ['booking', 'bundle', 'grouped'];

            $outputs = request()->all();
            if (isset($outputs['channel']))
                unset($outputs['channel']);
            if (isset($outputs['locale']))
                unset($outputs['locale']);
            if (isset($outputs['_token']))
                unset($outputs['_token']);

            foreach ($outputs as $type) {
                $productSetting = $type;
            }

            foreach ($productSetting as $value) {
                $allowProductType = $value;
            }

            foreach ($allowProductType as $key => $value) {
                $configData[] = $value['booking_active_option'] ? 'active' : 'inactive';
                $configData[] = $value['bundle_active_option'] ? 'active' : 'inactive';
                $configData[] = $value['grouped_active_option'] ? 'active' : 'inactive';
            }

            foreach ($productType as $key => $type) {
                SellerProductType::updateOrCreate(['product_type' => $productType[$key]], ['status' => $configData[$key]]);
            }
        }
    }
}
