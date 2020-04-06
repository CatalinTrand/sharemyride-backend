<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vehicle_id')->unsigned();
            $table->string('name', 50)->nullable();
            $table->string('short_name', 12)->nullable();
            $table->text('description')->nullable();
            $table->string('alimentazione', 30)->nullable();
            $table->string('cambio', 30)->nullable();
            $table->string('marce', 30)->nullable();
            $table->string('trazione', 30)->nullable();
            $table->string('bagagliaio', 30)->nullable();
            $table->string('passo', 30)->nullable();
            $table->string('massa', 30)->nullable();
            $table->string('cilindrata', 30)->nullable();
            $table->string('consumo_urbano', 30)->nullable();
            $table->string('consumo_extra_urbano', 30)->nullable();
            $table->string('consumo_misto', 30)->nullable();
            $table->string('emissioni_co2', 30)->nullable();
            $table->string('categoria_euro', 30)->nullable();
            $table->string('velocita_max', 30)->nullable();
            $table->string('accelerazione', 30)->nullable();
            $table->string('coppia_max_regime', 30)->nullable();
            $table->string('potenza_max_regime', 30)->nullable();
            $table->timestamps();
        });

        Schema::table('variations', function (Blueprint $table) {
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variations');
    }
}
