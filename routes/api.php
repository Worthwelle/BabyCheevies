<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => '/v1'], function () {
    Route::get('/version', function() {
        return response()->json([
            'app' => config('app.name'),
            'version' => config('app.version'),
            'api' => 'v1'
        ]);
    });
    Route::post('/register', 'Auth\RegisterController@register');
    Route::get('/activate/{token}', 'Auth\RegisterController@activate');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/logout', 'Auth\LoginController@logout');

        Route::get('/test', function () {
            return response()->json([
                'authenticated'
            ]);
        });
    });
});
