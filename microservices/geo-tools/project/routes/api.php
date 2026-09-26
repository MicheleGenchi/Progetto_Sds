<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GpsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::get('home', [Controller::class, 'home']);
Route::post('cittaFiltrate', [CityController::class, 'get']);
Route::post('nazioniFiltrate', [CountryController::class, 'get']);
Route::post('verifica_posizione', [GpsController::class, 'verifica_posizione']);

