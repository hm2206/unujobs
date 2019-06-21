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
            $table->string("numero_de_documento")->unique();
            $table->string("departamento");
            $table->string("provincia");
            $table->string("distrito");
            $table->date("fecha_de_nacimiento");
            $table->string("phone");
            $table->string("email")->nullable();
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
