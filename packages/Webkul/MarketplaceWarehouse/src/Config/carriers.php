<?php

return [
    'mpwarehouse' => [
        'code'          => 'mpwarehouse',
        'title'         => 'Warehouse Shipping',
        'description'   => 'Warehouse Shipping',
        'active'        => true,
        'default_rate'  => 20,
        'type'          => 'per_unit',
        'class'         => 'Webkul\MarketplaceWarehouse\Carriers\MarketplaceWarehouse',
    ]
];