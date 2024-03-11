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
        Schema::create('warehouse_prices', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('price', 12, 4)->default(0);

            $table->integer('price_type_id')->unsigned();
            $table->foreign('price_type_id')->references('id')->on('warehouse_price_type')->onDelete('cascade');
           
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_prices');
    }
};
