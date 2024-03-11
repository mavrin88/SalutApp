<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_regions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('region_name')->nullable();
            $table->integer('max_weight')->nullable();

            $table->integer('delivery_type_id')->nullable();

            $table->integer('delivery_time_id')->nullable();

            $table->integer('warehouse_id')->unsigned();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');

            $table->integer('price_type_id')->unsigned();
            $table->foreign('price_type_id')->references('id')->on('warehouse_price_type')->onDelete('cascade');

            $table->integer('marketplace_seller_id')->unsigned();
            $table->foreign('marketplace_seller_id')->references('id')->on('marketplace_sellers')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_region');
    }
};
