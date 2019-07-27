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
            $table->integer('work_id');
            $table->integer('cargo_id');
            $table->integer('categoria_id');
            $table->integer('meta_id');
            $table->integer('fuente_id')->nullable();
            $table->integer('planilla_id')->nullable();
            $table->string('plaza')->nullable();
            $table->string('perfil')->nullable();
            $table->string('escuela')->nullable();
            $table->integer('active')->default(1);
            $table->text('observacion')->nullable();
            $table->string('ruc')->nullable();
            $table->double("total")->default(0);
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
