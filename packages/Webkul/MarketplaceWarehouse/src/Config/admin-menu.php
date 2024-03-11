<?php

return [
    [
        'key'   => 'marketplace.warehouse',
        'name'  => 'marketplace_warehouse::app.admin.layouts.warehouse',
        'route' => 'admin.warehouse.cities.index',
        'sort'  => 6
    ], [
        'key'   => 'marketplace.warehouse.cities',
        'name'  => 'marketplace_warehouse::app.admin.cities.title',
        'route' => 'admin.warehouse.cities.index',
        'sort'  => 1
    ], [
        'key'   => 'marketplace.warehouse.delivery-type',
        'name'  => 'marketplace_warehouse::app.admin.delivery-type.title',
        'route' => 'admin.warehouse.delivery_type.index',
        'sort'  => 2
    ], [
        'key'   => 'marketplace.warehouse.delivery-time',
        'name'  => 'marketplace_warehouse::app.admin.delivery-time.title',
        'route' => 'admin.warehouse.delivery_time.index',
        'sort'  => 3
    ],
];