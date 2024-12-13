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
Route::get('cittaFiltrate', [CityController::class, 'get']);
Route::get('nazioniFiltrate', [CountryController::class, 'get']);
Route::get('verifica_posizione', [GpsController::class, 'verifica_posizione']);

