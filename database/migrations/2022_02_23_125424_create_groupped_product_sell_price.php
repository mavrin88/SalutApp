<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGrouppedProductSellPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mp_grouped_product_price', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_grouped_product_id');
            $table->foreign('product_grouped_product_id')->references('id')->on('product_grouped_products')->onDelete('cascade');
            $table->unsignedInteger('marketplace_seller_id');
            $table->foreign('marketplace_seller_id')->references('id')->on('marketplace_sellers')->onDelete('cascade');
            
            $table->double('seller_sell_price',8,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mp_grouped_product_price');
    }
}
