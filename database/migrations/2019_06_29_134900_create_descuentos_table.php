<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDescuentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descuentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("work_id");
            $table->integer("cargo_id");
            $table->integer("categoria_id");
            $table->integer("planilla_id");
            $table->integer("meta_id");
            $table->integer("dias")->default(30);
            $table->integer("cronograma_id");
            $table->text("observaciones")->nullable();
            $table->integer("sede_id")->default(1);
            $table->integer("type_descuento_id");
            $table->integer("mes")->default(6);
            $table->integer("año")->default(2019);
            $table->double("monto")->default(0);
            $table->integer("adicional")->default(0);
            $table->string("horas")->default(8);
            $table->integer("base")->default(0);
            $table->integer("edit")->default(1);
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
        Schema::dropIfExists('descuentos');
    }
}
