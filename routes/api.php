<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckValidId;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
 * here we are defining the end points 
 */
Route::post('orders', 'OrderController@create');
Route::middleware(CheckValidId::class)->patch('orders/{id}', 'OrderController@update');
Route::get('orders', 'OrderController@index');
