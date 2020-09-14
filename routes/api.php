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
/* Oauth */
Route::post('oauth/login', 'User\UserController@login');
Route::post('oauth/refresh', 'User\UserController@refresh');
Route::post('oauth/logout', 'User\UserController@logout');

/* USER */
Route::get('users', 'User\UserController@index');
Route::get('users/{id}', 'User\UserController@show')->middleware('auth:api');
Route::post('users', 'User\UserController@store')->middleware('auth:api');
Route::put('users/{id}', 'User\UserController@update')->middleware('auth:api');
Route::delete('users/{id}', 'User\UserController@destroy')->middleware('auth:api');

//TEST URL
Route::get('tests/test/success', 'Test\TestController@success');
Route::get('tests/test/fail', 'Test\TestController@fail');
