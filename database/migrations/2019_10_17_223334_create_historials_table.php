<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistorialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('work_id')->nullable();
            $table->bigInteger('info_id');
            $table->bigInteger('planilla_id')->nullable();
            $table->bigInteger('cargo_id')->nullable();
            $table->bigInteger('categoria_id')->nullable();
            $table->bigInteger('meta_id')->nullable();
            $table->bigInteger('cronograma_id');
            $table->bigInteger('fuente_id')->nullable();
            $table->bigInteger('sindicato_id')->nullable();
            $table->bigInteger('afp_id')->nullable();
            $table->bigInteger('type_afp_id')->nullable();
            $table->string('numero_de_cussp')->nullable();
            $table->date("fecha_de_afiliacion")->nullable();
            $table->bigInteger('banco_id')->nullable();
            $table->string('numero_de_cuenta')->nullable();
            $table->string('numero_de_essalud')->nullable();
            $table->string('plaza')->nullable();
            $table->string('perfil')->nullable();
            $table->string('escuela')->nullable();
            $table->string("pap")->nullable();
            $table->string('ext_pptto')->nullable();
            $table->text('observacion')->nullable();
            $table->double('base')->default(0);
            $table->double('base_enc')->default(0);
            $table->double('total_bruto')->default(0);
            $table->double('total_neto')->default(0);
            $table->double('total_desct')->default(0);
            $table->string('pdf')->nullable();
            $table->tinyInteger('afecto')->default(1);
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
        Schema::dropIfExists('historials');
    }
}
