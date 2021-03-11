<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('work_id');
            $table->bigInteger('planilla_id');
            $table->bigInteger('cargo_id');
            $table->bigInteger('categoria_id');
            $table->bigInteger('meta_id');
            $table->bigInteger('fuente_id')->nullable();
            $table->bigInteger('sindicato_id')->nullable();
            $table->bigInteger('afp_id')->nullable();
            $table->bigInteger('type_afp_id')->nullable();
            $table->string('numero_de_cussp')->nullable();
            $table->date("fecha_de_afiliacion")->nullable();
            $table->bigInteger('banco_id')->nullable();
            $table->string('numero_de_cuenta')->default('');
            $table->string('numero_de_essalud')->nullable();
            $table->string('fuente')->nullable();
            $table->string('plaza')->nullable();
            $table->string('perfil')->nullable();
            $table->string('escuela')->nullable();
            $table->string('ruc')->nullable();
            $table->string("pap")->nullable();
            $table->date("fecha_de_ingreso");
            $table->date("fecha_de_cese")->nullable();
            $table->tinyInteger("afecto")->default(1);
            $table->tinyInteger("especial")->default(0);
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('infos');
    }
}
