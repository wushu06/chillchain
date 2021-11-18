<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShipperController;
use App\Http\Controllers\ContactController;
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

Route::get('/shippers', [ShipperController::class, 'index']);
Route::get('/shippers/search/{name}', [ShipperController::class, 'search']);
Route::post('/shippers', [ShipperController::class, 'store']);
Route::put('/shippers/{id}', [ShipperController::class, 'update']);
Route::delete('/shippers/{id}', [ShipperController::class, 'destroy']);
Route::get('/shippers/contacts/{id}', [ShipperController::class, 'show']);

Route::get('/contacts', [ContactController::class, 'index']);

