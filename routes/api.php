<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\IndexController as ApiController;

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
Route::namespace('Api')->group(
    static function () {
        Route::get('test', [ApiController::class, 'index']);
    }
);

