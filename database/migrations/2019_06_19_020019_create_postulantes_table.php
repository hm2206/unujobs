<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostulantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postulantes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("ape_paterno");
            $table->string("ape_materno");
            $table->string("nombres");
            $table->string("nombre_completo")->nullable();
            $table->string("numero_de_documento")->unique();
            $table->integer("departamento_id");
            $table->integer("provincia_id");
            $table->integer("distrito_id");
            $table->date("fecha_de_nacimiento");
            $table->string("phone");
            $table->string("email")->nullable();
            $table->string("cv")->nullable();
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
        Schema::dropIfExists('postulantes');
    }
}
