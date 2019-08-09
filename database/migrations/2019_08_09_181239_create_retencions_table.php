<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRetencionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retencions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("cronograma_id");
            $table->integer("work_id");
            $table->integer("cargo_id");
            $table->string("mes");
            $table->string("año");
            $table->double("monto")->default(0);
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
        Schema::dropIfExists('retencions');
    }
}
