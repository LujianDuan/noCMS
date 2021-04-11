<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('passport')->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/login','PassportController@login')
        ->withoutMiddleware('auth:api')
        ->name('passport.login');

    Route::get('/routes','SystemController@routes')
        ->name('system.routes');

});
Route::prefix('admin')->group(function(){
    Route::get('/list','AdminController@list')
        ->name('admin.list');
});
