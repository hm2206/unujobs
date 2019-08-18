<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigDescuentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_descuentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("type_descuento_id");
            $table->double("porcentaje")->default(0);
            $table->double("minimo")->default(0);
            $table->double("monto")->default(0);
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
        Schema::dropIfExists('config_descuentos');
    }
}
