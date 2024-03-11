<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerProducttype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_producttype', function (Blueprint $table) {
            $table->id();
        
            $table->unsignedInteger('marketplace_sellers_id');
            $table->foreign('marketplace_sellers_id')->references('id')->on('marketplace_sellers')->onDelete('cascade');

            $table->string('product_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seller_producttype');
    }
}
