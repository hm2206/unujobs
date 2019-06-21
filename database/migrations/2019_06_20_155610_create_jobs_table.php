<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("ape_paterno");
            $table->string("ape_materno");
            $table->string("nombres");
            $table->string("nombre_completo")->nullable();
            $table->string("tipo_documento_id")->default(1);
            $table->string("numero_de_documento")->unique();
            $table->string("fecha_de_nacimiento");
            $table->string("profesion");
            $table->string("phone");
            $table->date("fecha_de_ingreso");
            $table->integer("sexo");
            $table->string("numero_de_essalud")->unique();
            $table->integer("banco_id")->nullable();
            $table->string("numero_de_cuenta")->unique();
            $table->integer("afp_id")->nullable();
            $table->date("fecha_de_afiliacion");
            $table->string("plaza")->nullable();
            $table->string("numero_de_cussp")->nullable();
            $table->integer("accidentes")->default(0);
            $table->integer("sindicato_id")->nullable();
            $table->integer("categoria_id");
            $table->integer("cargo_id");
            $table->text("observaciones")->nullalbe();
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
        Schema::dropIfExists('jobs');
    }
}
