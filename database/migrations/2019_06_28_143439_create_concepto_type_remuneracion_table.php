<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptoTypeRemuneracionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concepto_type_remuneracion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("concepto_id");
            $table->integer("type_remuneracion_id");
            $table->integer("categoria_id");
            $table->double("monto")->nullalbe();
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
        Schema::dropIfExists('concepto_type_remuneracion');
    }
}
