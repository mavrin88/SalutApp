<?php

return [
    'shop' => [
        'warehouse' => [
            'id'                    => 'id',
            'date'                  => 'Date',
            'title'                 => 'Warehouse',
            'create'                => 'Create',
            'delete'                => 'Delete',
            'create-title'          => 'Add Warehouse',
            'edit-title'            => 'Edit Warehouse',
            'save-title'            => 'Save Warehouse',
            'general'               => 'General',
            'warehouse-name'        => 'Warehouse Name',
            'warehouse-desc'        => 'Warehouse Description',
            'create-warehouse'      => 'Warehouse Created Successfully.',
            'update-warehouse'      => 'Warehouse Updated Successfully.',
            'delete-success'        => 'Warehouse deleted successfully.',
            'delete-failed'         => 'Error encountered while deleting Warehouse.',
            'mass-delete-success'   => 'All the Selected Warehouses have been deleted successfully.',
            'max-weight'            => 'Maximum Weight',
            'delivery'              => 'Delivery',
            'delivery-charges'      => 'Delivery Charges',
            'cities'                => 'Cities',
            'select-cities'         => 'Select Cities',
            'from'                  => 'From',
            'to'                    => 'To',
            'cost'                  => 'Cost',

            'receipt-and-withdrawal' => [
                'title'                             =>  'Receipts and Withdrawals',
                'create'                            =>  'Create',
                'view-title'                        =>  'Receipts and Withdrawals #:receipt_id',
                'created-on'                        =>  'Created On',
                'warehouse-name'                    =>  'Warehouse Name',
                'products-quantity-added'           =>  'Added Products Quantity',
                'created-receipt-and-withdrawal'    =>  'Receipts and Withdrawals Created Successfully.',
                
                'products'                           =>  [
                    'title'                          => 'Title',
                    'search-title'                   => 'Search Products',
                    'enter-search-term'              => 'Type atleast 3 Characters',
                    'no-result-found'                => 'Products not found with this name.',
                    'searching'                      => 'Searching ...',
                    'name'                           => 'Name',
                    'sku'                            => 'SKU',
                    'qty'                            => 'Quantity',
                    'price'                          => 'Price',
                    'info'                           => 'Information',
                ]
            ],

            'price'                  => [
                'title'              =>  'Price Type',
                'price-title'        =>  'Title',
                'create-title'       =>  'Add Price Type',
                'save-title'         =>  'Save',
                'id'                 =>  'Id',
                'product-count'      =>  'Number of Products',
                'amt'                =>  'Price',
                'add-product'        =>  'Assigned atleast 1 Product with the Price Type.',
                'product-exist'      =>  'Given Product Type title already Exist',
                'create-success'     =>  'Price Type Created Successfully.',
                'update-success'     =>  'Price Type Updated Successfully.',

            ],

            'region'                 => [
                'region-name'       => 'Region Name',
                'title'             => 'Region',
                'region-title'      => 'Add Region',
                'edit-title'        => 'Edit Region',
                'discount'          => 'Discount',
                'update-success'    => 'Region Updated Successfully.',
                'update-failed'     => 'Region does not Exit.',
                'save'              => 'Save',
            ]
        ],

        'location'  =>  [
            'add-location'          =>  'Add Location',
            'enter-location'        =>  'Enter Location',
            'go-to-shop'            =>  'Go to Shop',
        ],

        'cart' => [
            'empty'     => 'On location address change cart will empty.',
        ]
    ],  

    'admin' =>  [
        'layouts'               => [ 
            'warehouse'         =>  'Warehouse',
            'create-btn-title'  =>  'Save',
            'id'                =>  'Id',
        ],

        'cities'                => [
            'title'             =>  'City',
            'add-btn-title'     =>  'Add City',
            'create-success'    =>  'City Created Successfully.',
            'edit-title'        =>  'Edit City',
            'update-success'    =>  'City has been Updated Successfully',
            'delete-success'    =>  'City has been Deleted Successfully',
        ],

        'delivery-type'         => [
            'title'             =>  'Delivery Type',
            'add-btn-title'     =>  'Add Delivery Type',
            'create-success'    =>  'Delivery Type Created Successfully.',
            'edit-title'        =>  'Edit Delivery Type',
            'update-success'    =>  'Delivery Type has been Updated Successfully',
            'delete-success'    =>  'Delivery Type has been Deleted Successfully',
        ],

        'delivery-time'         => [
            'title'             =>  'Delivery Time',
            'add-btn-title'     =>  'Add Delivery Time',
            'create-success'    =>  'Delivery Time Created Successfully.',
            'edit-title'        =>  'Edit Delivery Time',
            'update-success'    =>  'Delivery Time has been Updated Successfully',
            'delete-success'    =>  'Delivery Time has been Deleted Successfully',
        ],

        'system' => [
            'title'                 => 'Title',
            'description'           => 'Description',
            'rate'                  => 'Rate',
            'type'                  => 'Type',
            'status'                => 'Status',
            'table-rate-shipping'   => 'Warehouse Shipping',
        ],
    ]
];
