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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//Route::post('auth/register', 'AuthController@register');
//Route::post('auth/login', 'AuthController@login');
//Route::get('users', 'UserController@users');
//Route::get('users/{id}', 'UserController@profileById')->middleware('auth:api');

//Route::post('post', 'PostController@add')->middleware('auth:api');
//Route::put('post/{post}', 'PostController@update')->middleware('auth:api');
//Route::delete('post/{post}', 'PostController@delete')->middleware('auth:api');

//Route::get('users/profile', 'UserController@profile')->middleware('auth:api');


//public routes
Route::post('client-create', 'ClientController@store');
Route::post('user-create', 'UserController@store');

//private routes
Route::group(['middleware' => 'token.verification'], function() {

    Route::put('client-update/{client}', 'ClientController@update');
    Route::delete('client-delete/{id}', 'ClientController@delete');
    Route::get('user/{id}', 'UserController@show');
    Route::put('user-update/{id}', 'UserController@update');
    Route::delete('user-delete/{id}', 'UserController@destroy');

});