<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDenunciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('denuncias', function (Blueprint $table) {
            $table->id();
            $table->datetime('fecha_denuncia');
            $table->datetime('fecha_solucion')->nullable();
            $table->string('ubicacion');
            $table->integer('estado')->default(0);
            $table->string('descripcion_denuncia');
            $table->string('descripcion-solucion')->nullable();
            $table->foreignId('id_user')->references('id')->on('usuarios');
            $table->foreignId('id_barrio')->references('id')->on('barrios');
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
        Schema::dropIfExists('denuncias');
    }
}
