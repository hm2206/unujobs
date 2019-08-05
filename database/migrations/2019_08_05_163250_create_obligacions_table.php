<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObligacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obligacions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("beneficiario");
            $table->string("numero_de_documento");
            $table->string("numero_de_cuenta");
            $table->double("monto")->default(0);
            $table->bigInteger("work_id");
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
        Schema::dropIfExists('obligacions');
    }
}
