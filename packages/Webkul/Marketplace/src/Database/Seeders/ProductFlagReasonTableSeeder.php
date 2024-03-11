<?php

namespace Webkul\Marketplace\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductFlagReasonTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('marketplace_product_flag_reasons')->delete();

        DB::table('marketplace_product_flag_reasons')->insert([
            [
                'id' => 1,
                'reason' => 'Duplicate product',
                'status' => true
            ], [
                'id' => 2,
                'reason' => 'Damaged product',
                'status' => true
            ], [
                'id' => 3,
                'reason' => 'Poor product quality',
                'status' => true
            ], [
                'id' => 4,
                'reason' => 'Over price product',
                'status' => true
            ], [
                'id' => 5,
                'reason' => 'Missing product parts',
                'status' => true
            ], [
                'id' => 6,
                'reason' => 'Recieve wrong product',
                'status' => true
            ]
        ]);
    }
}
