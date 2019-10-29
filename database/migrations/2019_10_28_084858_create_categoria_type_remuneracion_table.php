<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriaTypeRemuneracionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categoria_type_remuneracion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('categoria_id');
            $table->bigInteger('type_remuneracion_id');
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
        Schema::dropIfExists('categoria_type_remuneracion');
    }
}
