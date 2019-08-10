<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeRemuneracionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_remuneracions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("key")->unique();
            $table->string("descripcion");
            $table->double("monto")->nullable();
            $table->integer('base')->default(0);
            $table->integer('bonificacion')->default(0);
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
        Schema::dropIfExists('type_remuneracions');
    }
}
