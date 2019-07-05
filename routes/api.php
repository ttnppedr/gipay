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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/pay', 'CommandsController@pay')->middleware('CheckFormatIsCorrect', 'CheckAccountIsBlocked');
Route::post('/record', 'CommandsController@record');
Route::post('/balance', 'CommandsController@balance');
Route::post('/callback', 'CommandsController@callback');

Route::post('/login', 'AdminController@login');
Route::post('/deposit/{toUser}', 'AdminController@deposit');
Route::post('/withdraw/{toUser}', 'AdminController@withdraw');

Route::get('/admin/users', 'UserController@index');
Route::patch('/admin/block/user/{user}', 'UserController@block');
Route::patch('/admin/unblock/user/{user}', 'UserController@unblock');
Route::get('/orders/{user}', 'UserController@indexOrder');

Route::get('/admin/orders', 'OrderController@index');
