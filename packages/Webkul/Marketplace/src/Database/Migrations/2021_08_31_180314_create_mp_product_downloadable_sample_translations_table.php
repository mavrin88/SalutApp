<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMpProductDownloadableSampleTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mp_product_downloadable_sample_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('locale');
            $table->text('title')->nullable();

            $table->integer('mp_product_downloadable_sample_id')->unsigned();
            $table->foreign('mp_product_downloadable_sample_id', 'mp_sample_translations_sample_id_foreign')->references('id')->on('mp_product_downloadable_samples')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_downloadable_sample_translations');
    }
}
