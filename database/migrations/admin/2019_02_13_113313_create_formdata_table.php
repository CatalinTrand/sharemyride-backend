<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormdataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_data', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('business');
            $table->string( 'name', 50);
            $table->integer('phone');
            $table->string( 'email', 30);
            $table->string( 'region', 20)->nullable();
            $table->string( 'tax_id', 20)->nullable();
            $table->string( 'fiscal_code', 20)->nullable();
            $table->string( 'province', 20)->nullable();
            $table->string( 'note', 100)->nullable();
            $table->string( 'brand', 20);
            $table->string( 'vehicle', 20);
            $table->string( 'variant', 20);
            $table->integer( 'anticipo');
            $table->integer( 'percorrenza');
            $table->integer( 'durata')->nullable();
            $table->integer( 'price');
            $table->integer( 'promo_price')->nullable();
            $table->string( 'user_ip', 20)->nullable();
            $table->string('year', 4)->nullable();
            $table->string('month', 20)->nullable();
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
        Schema::dropIfExists('form_data');
    }
}
