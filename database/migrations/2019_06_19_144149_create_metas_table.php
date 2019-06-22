<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("metaID");
            $table->string('meta');
            $table->string("sectorID");
            $table->string("sector");
            $table->string("pliegoID");
            $table->string("pliego");
            $table->string("unidadID");
            $table->string("unidad_ejecutora");
            $table->string("programaID");
            $table->string("programa");
            $table->string("funcionID");
            $table->string("funcion");
            $table->string("subProgramaID");
            $table->string("sub_programa");
            $table->string("actividadID");
            $table->string("actividad");
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
        Schema::dropIfExists('metas');
    }
}
