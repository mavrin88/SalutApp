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
        Schema::create('warehouse_assigned_cities', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('warehouse_region_id')->unsigned();
            $table->foreign('warehouse_region_id')->references('id')->on('warehouse_regions')->onDelete('cascade');

            $table->integer('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('warehouse_delivery_city')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_assigned_cities');
    }
};
