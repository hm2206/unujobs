<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemuneracionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remuneracions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("sede_id")->default(1);
            $table->bigInteger("info_id");
            $table->bigInteger("work_id");
            $table->bigInteger("categoria_id");
            $table->bigInteger("cargo_id");
            $table->bigInteger("planilla_id");
            $table->bigInteger("meta_id");
            $table->bigInteger("cronograma_id");
            $table->bigInteger("type_remuneracion_id");
            $table->integer("dias")->default(30);
            $table->text("observaciones")->nullable();
            $table->integer("mes")->default(6);
            $table->integer("año")->default(2019);
            $table->double("monto")->default(0);
            $table->integer("adicional")->default(0);
            $table->string("horas")->default(8);
            $table->integer('base')->default(0);
            $table->integer('activo')->default(1);
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
        Schema::dropIfExists('remuneracions');
    }
}
