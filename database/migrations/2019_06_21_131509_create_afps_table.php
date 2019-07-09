<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAfpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('afps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("nombre");
            $table->text("descripcion")->nullable();
            $table->double("porcentaje")->nullable();
            $table->double('flujo')->nullable();
            $table->double('mixta')->nullable();
            $table->double('aporte')->nullable();
            $table->double('prima')->nullable();
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
        Schema::dropIfExists('afps');
    }
}
