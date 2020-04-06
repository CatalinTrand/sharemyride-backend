<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30);
            $table->string('surname', 30);
            $table->string('email', 50)->unique();
            $table->string('password', 100);
            $table->string('cellulare', 15)->nullable();
            $table->string('telefono', 15)->nullable();
            $table->string('image', 100)->nullable();
            $table->string('address', 50)->nullable();
            $table->string('city', 20)->nullable();
            $table->string('last_log', 30)->nullable();
            $table->text('auth_token')->nullable();
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
        Schema::dropIfExists('users');
    }
}
