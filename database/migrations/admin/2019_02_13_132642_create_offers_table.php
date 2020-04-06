<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('variation_id')->unsigned();
            $table->string('default_image')->nullable();
            $table->text('description')->nullable();
            $table->string('interni', 30)->nullable();
            $table->string('offer_type', 10)->nullable();
            $table->string('anticipo_1')->nullable();
            $table->string('anticipo_2')->nullable();
            $table->string('anticipo_3')->nullable();
            $table->string('anticipo_4')->nullable();
            $table->string('anticipo_privati')->nullable();
            $table->string('prezzo_privati')->nullable();
            $table->string('prezzo_privati_discount')->nullable();
            $table->integer('tag_id')->unsigned()->nullable();
            $table->boolean('in_slider')->default(false);
            $table->integer('slide_order')->nullable();
            $table->timestamps();
        });

        Schema::table('offers', function (Blueprint $table) {
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('offers_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
