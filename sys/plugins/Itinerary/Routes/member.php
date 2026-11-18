<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 7/1/2017
 * Time: 10:02 AM
 */
use Illuminate\Http\Request;

Route::prefix('itinerary')->group(function() {

    Route::prefix('/member')->group(function() {
        Route::get('/', 'MemberController@index');

        Route::get('/profile', 'ProfileController@index');

        Route::post('/profile', function(Request $request) {
            return (new \Modules\Itinerary\Member\ProfileController())->save($request);
        });

        Route::post('/profile/theme', 'ProfileController@theme');

        Route::post('/profile/location', 'ProfileController@location');

        Route::get('/user', 'UserController@index');

        Route::get('/user/create', 'UserController@create');

        Route::get('/user/edit/{id}', 'UserController@edit');

        Route::post('/user/store/{id}', 'UserController@store');

        Route::get('/user/changepassword/{id}', 'UserController@password');

        Route::post('/user/changepassword/{id}', 'UserController@changepass');

        Route::get('/form', 'FormController@index');

        Route::get('/form/preview', 'FormController@preview');

        Route::post('/form', 'FormController@store');

        Route::get('/widget', 'WidgetController@index');

        Route::match(['get', 'post'], '/inquiry', 'InquiryController@index');

        Route::post('/inquiry/register', 'InquiryController@store');

        Route::get('/inquiry/getFormData/{id}', 'InquiryController@getFormData');

        Route::post('/inquiry/getUpdateStatus', 'InquiryController@getUpdateStatus');

        Route::post('/inquiry/editBulk', 'InquiryController@editBulk');

        Route::post('/inquiry/setUserPushToken', 'InquiryController@setUserPushToken');

        Route::get('/content', 'ContentController@index');

        Route::get('/tour', 'TourController@index');

        Route::get('/tour/arranged', 'TourController@arranged');

        Route::get('/tour/recommended', 'TourController@recommended');

        Route::get('/tour/create', 'TourController@create');

        Route::get('/tour/edit/{id}', 'TourController@edit')->name('tour.member.edit');

        Route::post('/tour/store/{id}', 'TourController@store')->name('tour.member.store');

        Route::post('/tour/storeArranged/{id}', 'TourController@storeArranged');

        Route::post('/tour/bulkEdit', 'TourController@bulkEdit');

        Route::get('/tour/getPredefinedSpots/{id}', 'TourController@getPredefinedSpots');

        Route::get('/tour/getSpotsDetailByGeoLocation', 'TourController@getSpotsDetailByGeoLocation');

        Route::get('/location', 'LocationController@index');

        Route::post('/location/store/{id}', 'LocationController@store');

        Route::get('/location/edit/{id}', 'LocationController@edit');

        Route::post('/location/editBulk', 'LocationController@editBulk');

        Route::post('/widget/bulkEdit', 'WidgetController@bulkEdit');

        Route::post('/widget/changeStatus', 'WidgetController@changeStatus');

        Route::get('/widget/create', 'WidgetController@create')->name('widget.member.create');

        Route::get('/widget/edit/{id}', 'WidgetController@edit')->name('widget.member.edit');

        Route::post('/widget/store/{id}', 'WidgetController@store')->name('widget.member.store');

        Route::post('/widget/republish-code/{id}', 'WidgetController@republishCode')->name('widget.member.republish.code');

        Route::get('/updateInfo', 'UpdateInfoController@index');
    });

});
