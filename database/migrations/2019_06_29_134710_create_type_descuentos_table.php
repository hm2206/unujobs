<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeDescuentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_descuentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("key")->unique();
            $table->string("descripcion");
            $table->double("monto")->default();
            $table->string('config_afp')->nullable();
            $table->integer('obligatorio')->default(0);
            $table->integer('ley')->default(0);
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
        Schema::dropIfExists('type_descuentos');
    }
}
