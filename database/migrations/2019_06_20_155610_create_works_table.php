<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("ape_paterno");
            $table->string("ape_materno");
            $table->string("nombres");
            $table->string("nombre_completo")->nullable();
            $table->string("direccion")->nullable();
            $table->string("tipo_documento_id")->default(1);
            $table->string("numero_de_documento")->unique();
            $table->string("fecha_de_nacimiento");
            $table->string("profesion");
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->date("fecha_de_ingreso");
            $table->integer("sexo");
            $table->string("numero_de_essalud")->nullable();
            $table->integer("banco_id")->nullable();
            $table->string("numero_de_cuenta")->nullable();
            $table->integer("afp_id")->nullable();
            $table->string('type_afp')->nullable();
            $table->date("fecha_de_afiliacion")->nullable();
            $table->string("plaza")->nullable();
            $table->string("numero_de_cussp")->nullable();
            $table->integer("accidentes")->default(0);
            $table->integer("sindicato_id")->nullable();
            $table->integer("sede_id")->default(1);
            $table->integer("planilla_id")->nullable();
            $table->string('ley')->nullable();
            $table->double('total')->nullable();
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
        Schema::dropIfExists('works');
    }
}
