<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEducacionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('educacionals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('type_educacional_id');
            $table->bigInteger('type_descuento_id');
            $table->bigInteger('work_id');
            $table->bigInteger('info_id');
            $table->bigInteger('historial_id');
            $table->bigInteger('cronograma_id');
            $table->double('monto')->default(0);
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
        Schema::dropIfExists('educacionals');
    }
}
