<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAfpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("nombre");
            $table->double('prima')->nullable();
            $table->double('aporte')->nullable();
            $table->bigInteger('prima_descuento_id')->nullable();
            $table->bigInteger('aporte_descuento_id')->nullable();
            $table->double('prima_limite')->default(0);
            $table->tinyInteger('activo')->default(1);
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
        Schema::dropIfExists('afps');
    }
}
