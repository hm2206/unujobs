<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCronogramasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cronogramas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("mes")->nullable();
            $table->integer("año")->nullable();
            $table->integer("planilla_id");
            $table->integer("adicional")->default(0);
            $table->integer("sede_id")->default(1);
            $table->integer("numero")->nullable();
            $table->integer("dias")->default(30);
            $table->string('pdf')->nullable();
            $table->integer('pendiente')->default(0);
            $table->text('observacion')->nullable();
            $table->tinyInteger("estado")->default(0);
            $table->tinyInteger('auto')->default(1);
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
        Schema::dropIfExists('cronogramas');
    }
}
