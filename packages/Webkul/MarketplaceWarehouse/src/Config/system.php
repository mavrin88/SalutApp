<?php

return [
    [
        'key'    => 'sales.carriers.mpwarehouse',
        'name'   => 'marketplace_warehouse::app.admin.system.table-rate-shipping',
        'sort'   => 3,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'marketplace_warehouse::app.admin.system.title',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true
            ], [
                'name'          => 'description',
                'title'         => 'marketplace_warehouse::app.admin.system.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true
            ], [
                'name'       => 'type',
                'title'      => 'admin::app.admin.system.type',
                'type'       => 'select',
                'options'    => [
                    [
                        'title' => 'Per Unit',
                        'value' => 'per_unit',
                    ], [
                        'title' => 'Per Order',
                        'value' => 'per_order',
                    ]
                ],
                'validation' => 'required'
            ],  [
                'name'          => 'active',
                'title'         => 'admin::app.admin.system.status',
                'type'          => 'boolean',
                'validation'    => 'required',
                'channel_based' => true,
                'locale_based'  => false,
            ],
        ]
    ],
];