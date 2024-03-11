<?php

namespace Webkul\Marketplace\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellerFlagReasonTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('marketplace_seller_flag_reasons')->delete();

        DB::table('marketplace_seller_flag_reasons')->insert([
            [
                'id' => 1,
                'reason' => 'Duplicate product sold by seller',
                'status' => true
            ], [
                'id' => 2,
                'reason' => 'Damaged product sold by seller',
                'status' => true
            ], [
                'id' => 3,
                'reason' => 'Poor product quality sold by seller',
                'status' => true
            ], [
                'id' => 4,
                'reason' => 'Over price product sold by seller',
                'status' => true
            ], [
                'id' => 5,
                'reason' => 'Wrong product sold by seller',
                'status' => true
            ]
        ]);
    }
}
