<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("numero_de_requerimiento")->unique();
            $table->integer("sede_id");
            $table->integer("dependencia_id");
            $table->integer("oficina_id")->nullable();
            $table->integer("cargo_id")->nullable();
            $table->integer("cantidad")->default(1);
            $table->double("honorarios")->nullable();
            $table->integer("meta_id")->nullable();
            $table->integer("fuente_id")->nullable();
            $table->double("gasto")->nullable();
            $table->date("periodo")->nullable();
            $table->integer("lugar_id")->nullable();
            $table->text("perfil")->nullable();
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
        Schema::dropIfExists('personals');
    }
}
