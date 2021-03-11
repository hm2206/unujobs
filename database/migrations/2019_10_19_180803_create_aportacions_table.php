<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAportacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aportacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('work_id');
            $table->bigInteger('info_id');
            $table->bigInteger('historial_id');
            $table->bigInteger('cronograma_id');
            $table->bigInteger('type_descuento_id');
            $table->bigInteger('type_aportacion_id');
            $table->double('porcentaje')->default(0)->comment('porcentaje para calcular aportación');
            $table->double('minimo')->default(0)->comment('monto minimo para calcular aportacion');
            $table->double('default')->default(0)->comment('monto por defecto a calcular la aportacion, depende del campo minimo');
            $table->double('monto')->default(0)->comment('monto automatico generado de la aportación');
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
        Schema::dropIfExists('aportacions');
    }
}
