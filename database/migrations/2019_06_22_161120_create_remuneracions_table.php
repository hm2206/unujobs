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
            $table->integer("job_id");
            $table->integer("categoria_id");
            $table->integer("dias")->default(30);
            $table->integer("concepto_id");
            $table->integer("cronograma_id");
            $table->text("observaciones")->nullable();
            $table->integer("sede")->default(1);
            $table->integer("mes")->default(6);
            $table->integer("año")->default(2019);
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
        Schema::dropIfExists('remuneracions');
    }
}
