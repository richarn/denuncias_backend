<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarrioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barrios', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->foreignId('id_ciudad')->references('id')->on('ciudades');
            $table->timestamps();
        });

        DB::table('barrios')->insert(
            array('id' => 1, 'descripcion' => 'Aquino cañada', 'id_ciudad' => 1, 'created_at' => Carbon\Carbon::now()),
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barrios');
    }
}
