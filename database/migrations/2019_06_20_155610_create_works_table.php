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
            $table->string("fecha_de_nacimiento")->nullable();
            $table->string("profesion")->nullable();
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->integer("sexo");
            $table->integer("activo")->default(1);
            $table->softDeletes();
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
