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

Route::apiResource('users', 'UserController');
Route::post('login', 'UserController@login');

Route::apiResource('rooms', 'RoomController');
Route::post('rooms/{room}/join', 'RoomController@join');
Route::post('rooms/{room}/leave', 'RoomController@leave');

Route::apiResource('message', 'MessageController');