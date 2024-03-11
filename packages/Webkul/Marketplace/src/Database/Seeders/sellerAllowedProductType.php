<?php

namespace Webkul\Marketplace\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Marketplace\Models\SellerProductType;

class sellerAllowedProductType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Add seeder code here
        SellerProductType::insert([
            [
                'product_type' => 'simple',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'product_type' => 'configurable',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'product_type' => 'virtual',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'product_type' => 'downloadable',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'product_type' => 'booking',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'product_type' => 'bundle',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'product_type' => 'grouped',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        echo "product type asigned to seller.";
    }
}
