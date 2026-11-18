<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('itinerary')->group(function() {
    Route::post('/inquiry/create', 'InquiryController@create');
    Route::get('/consult/{id}', 'ConsultController@index');
    Route::get('/chatting/{id}', 'ConsultController@getChattingHistory');
    Route::middleware('cors')->get('/getCurrentUser', 'TourController@getCurrentUser');

//    @terminator
    Route::post('/inquiry/setUserPushToken', 'InquiryController@setUserPushToken');
});
