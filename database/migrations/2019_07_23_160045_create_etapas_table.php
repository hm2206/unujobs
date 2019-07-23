<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEtapasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etapas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('postulante_id');
            $table->integer('type_etapa_id');
            $table->integer('convocatoria_id');
            $table->integer('personal_id');
            $table->integer('current')->default(1);
            $table->integer('next')->default(0);
            $table->integer('puntaje')->default(0);
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
        Schema::dropIfExists('etapas');
    }
}
