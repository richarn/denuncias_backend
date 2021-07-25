<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarriosController;
use App\Http\Controllers\DenunciasController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\RolesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('register/create-password', [AuthController::class, 'createPassword']);
    Route::get('register/activate/{token}', [AuthController::class, 'registerActivate']);
    
    Route::group(['middleware' => 'auth:api'], function() {
        Route::post('logout', [AuthController::class, 'logout']);
        //Route::put('user', [AuthController::class, 'update']);
        Route::get('user', [AuthController::class, 'user']);
        Route::put('usuarios/{idUser}', [AuthController::class, 'update']);
    });
});

Route::resource('barrios', BarriosController::class);
Route::resource('denuncias', DenunciasController::class);
Route::put('denuncias/{idDenuncia}/uploadImage', [DenunciasController::class, 'uploadImage']);
Route::delete('denuncias/{idDenuncia}/imagen/{idImagen}', [DenunciasController::class, 'removeImage']);

Route::resource('noticias', NoticiasController::class);
Route::resource('usuarios', AuthController::class);
Route::resource('roles', RolesController::class);
