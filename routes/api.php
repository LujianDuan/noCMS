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
    Route::post('/','AdminController@add')
        ->name('admin.add');
});




Route::get('/roles','RoleController@list')
    ->name('role.list');
Route::post('/role','RoleController@add')
    ->name('role.add');
Route::put('/role','RoleController@edit')
    ->name('role.edit');
Route::delete('/role','RoleController@delete')
    ->name('role.delete');

Route::get('/permissions','PermissionController@list')
    ->name('permission.list');
Route::post('/permission','PermissionController@add')
    ->name('permission.add');
Route::put('/permission','PermissionController@edit')
    ->name('permission.edit');
Route::delete('/permission','PermissionController@delete')
    ->name('permission.delete');

