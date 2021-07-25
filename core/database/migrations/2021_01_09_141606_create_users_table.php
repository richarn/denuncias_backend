<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('ci');
            $table->string('telefono');
            $table->string('activation_token')->nullable();
            $table->foreignId('id_barrio')->references('id')->on('barrios');
            $table->foreignId('id_role')->references('id')->on('roles');
            $table->tinyInteger('estado')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('usuarios')->insert(
            array(
                'id' => 1, 'name' => 'Admin', 'email' => 'admin@admin.com',
                'password' => bcrypt('admin'), 'ci' => 1234567, 'telefono' => '0987654321',
                'id_barrio' => 1, 'id_role' => 1,
                'created_at' => Carbon\Carbon::now()
            ),
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
