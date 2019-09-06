<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("planilla_id");
            $table->integer("adicional")->default(0);
            $table->bigInteger("meta_id");
            $table->string("ext_pptto");
            $table->string("escuela");
            $table->bigInteger("work_id");
            $table->string("plaza")->nullable();
            $table->bigInteger("cargo_id");
            $table->bigInteger("categoria_id");
            $table->string("pap")->nullable();
            $table->string("perfil")->nullable();
            $table->bigInteger("banco_id");
            $table->string("numero_de_cuenta")->nullable();
            $table->string("numero_de_essalud")->nullable();
            $table->string("numero_de_cussp")->nullable();
            $table->bigInteger("afp_id")->nullable();
            $table->string("type_afp")->nullable();
            $table->date("afp_fecha")->nullable();
            $table->integer("sexo")->default(1);
            $table->string("phone")->nullable();
            $table->date("fecha_de_nacimiento")->nullable();
            $table->date("fecha_de_ingreso")->nullable();
            $table->text("observacion")->nullable();
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
        Schema::dropIfExists('histories');
    }
}
