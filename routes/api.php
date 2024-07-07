<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', 'AuthController@login')->name('user.login');

// create by admin
Route::group(['prefix' => 'events', 'as' => 'event.'], function () {
    Route::get('/', ['as' => 'list', 'uses' => 'EventController@list']);
    Route::post('/', ['as' => 'create', 'uses' => 'EventController@create']);
    Route::get('/{id}', ['as' => 'query', 'uses' => 'EventController@query']);
    Route::put('/{id}', ['as' => 'edit', 'uses' => 'EventController@edit']);
    Route::delete('/{id}', ['as' => 'delete', 'uses' => 'EventController@delete']);
});

// currently manually approved/rejected for transaction
Route::group(['prefix' => 'transactions', 'as' => 'transaction.'], function () {
    Route::put('/{id}/status', ['as' => 'status', 'uses' => 'TransactionController@approvalStatus']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'bookings', 'as' => 'booking.'], function () {
        Route::post('/', ['as' => 'book', 'uses' => 'BookingController@book']);
        Route::get('/', ['as' => 'list', 'uses' => 'BookingController@list']);
        Route::get('/{id}', ['as' => 'query', 'uses' => 'BookingController@query']);
    });
});
