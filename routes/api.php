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

Route::group(['middleware' => ['api'], 'prefix' => '/v1'], function () {
    Route::get('/version', function() {
        return response()->json([
            'app' => config('app.name'),
            'version' => config('app.version'),
            'api' => 'v1'
        ]);
    });
    Route::post('auth/register', 'Auth\ApiRegisterController@register');
    Route::get('auth/activate/{token}', 'Auth\ApiRegisterController@activate');
});
