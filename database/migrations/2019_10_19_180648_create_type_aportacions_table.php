<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeAportacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_aportacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion');
            $table->double('porcentaje');
            $table->double('minimo')->default(0);
            $table->double('default')->default(0);
            $table->bigInteger('type_descuento_id');
            $table->tinyInteger('activo')->default(1);
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
        Schema::dropIfExists('type_aportacions');
    }
}
