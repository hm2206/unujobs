<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('alias');
            $table->string('logo');
            $table->string('email');
            $table->string('username');
            $table->string('copyright');
            $table->string('ruc')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ceo')->nullable();
            $table->string('cto')->nullable();
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
        Schema::dropIfExists('configs');
    }
}
