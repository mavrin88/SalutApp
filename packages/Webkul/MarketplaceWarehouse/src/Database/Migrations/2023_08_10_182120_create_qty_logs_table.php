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
        Schema::create('qty_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('associated_product_id')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('receipt_id')->unsigned();
            $table->foreign('receipt_id')->references('id')->on('receipts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qty_logs');
    }
};
