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

Route::middleware('auth:api')->get('/itinerary', function (Request $request) {
    return $request->user();
});
Route::get('getSpotsData/{hash}', '\Modules\Itinerary\Controllers\TourController@getSpotsData');
Route::get('getWidgetInfo/{widget_id}', '\Modules\Itinerary\Controllers\TourController@getWidgetInfo');
Route::get('getInquiryForm/{widget_id}', '\Modules\Itinerary\Controllers\TourController@getInquiryForm');
Route::get('getInquiry/{id}', '\Modules\Itinerary\Controllers\TourController@getInquiry');
Route::post('saveSpotsData', '\Modules\Itinerary\Controllers\TourController@saveSpotsData');
Route::post('submitInquiry', '\Modules\Itinerary\Controllers\TourController@saveInquiry');
Route::post('getSpotContent', '\Modules\Itinerary\Controllers\TourController@getSpotContent');
Route::get('loadChattingHistory', '\Modules\Itinerary\Controllers\TourController@loadChattingHistory');
Route::post('saveChattingHistory', '\Modules\Itinerary\Controllers\TourController@saveChattingHistory');
Route::post('getReservationDate', '\Modules\Itinerary\Controllers\TourController@getReservationDate');
