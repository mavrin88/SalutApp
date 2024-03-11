<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMpProductDownloadableLinkTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mp_product_downloadable_link_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('locale');
            $table->text('title')->nullable();

            $table->integer('mp_product_downloadable_link_id')->unsigned();
            $table->foreign('mp_product_downloadable_link_id', 'mp_link_translations_link_id_foreign')->references('id')->on('mp_product_downloadable_links')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mp_product_downloadable_link_translations');
    }
}
