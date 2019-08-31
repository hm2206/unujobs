<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfoCronogramaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('info_cronograma', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("cronograma_id");
            $table->bigInteger("info_id");
            $table->string("observacion")->nullable();
            $table->string("pap")->nullable();
            $table->string("ext_pptto")->nullable();
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
        Schema::dropIfExists('info_cronograma');
    }
}
