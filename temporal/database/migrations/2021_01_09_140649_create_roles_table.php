<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('nivel');
            $table->timestamps();
        });

        DB::table('roles')->insert(
            array('id' => 1, 'nombre' => 'Administrador', 'nivel' => 1, 'created_at' => Carbon\Carbon::now()),
            array('id' => 2, 'nombre' => 'Normal', 'nivel' => 2, 'created_at' => Carbon\Carbon::now()),
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
