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

/**
 * Public routes
 */
Route::post('client-create', 'ClientController@store');

/**
 * Private routes
 */
Route::group(['middleware' => 'token.verification'], function() {

    // clients 
    Route::get('client/{id}', 'ClientController@show');
    Route::put('client-update/{id}', 'ClientController@update');
    Route::delete('client-delete/{id}', 'ClientController@destroy');

    // users
   Route::post('user-create', 'UserController@store');
    Route::get('user/{id}', 'UserController@show');
    Route::put('user-update/{id}', 'UserController@update');
    Route::delete('user-delete/{id}', 'UserController@destroy');

    // user_phones
    Route::get('phone/{id}', 'PhoneController@show');
    Route::post('phone-create', 'PhoneController@store');
    Route::put('phone-update/{id}', 'PhoneController@update');
    Route::delete('phone-delete/{id}', 'PhoneController@destroy');

});