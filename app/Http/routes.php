<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api'], function() {

    Route::group(['prefix' => 'accounts'],function(){
        Route::post('signup','AccountController@apiPostSignUp');
        Route::post('get_existing_user', 'AccountController@verifyUserAcc');
        Route::post('login','AccountController@apiPostLogin');
        Route::post('logout', 'AccountController@apiPostLogout');
        Route::get('me', 'AccountController@apiGetAccount');
        Route::post('get_user', 'AccountController@apiGetUser');
        Route::post('update_user', 'AccountController@apiUpdateUser');
        Route::post('update_role', 'AccountController@apiUpdateRole');
    }); 

    Route::group(['prefix' => 'trip'],function(){
        Route::post('register_trip', 'TripsController@store');
        Route::post('update_trip/{id}', 'TripsController@updateTrip');
        Route::post('get_trips', 'TripsController@getTrips');
        Route::post('get_trip_details', 'TripsController@getTripDetails');
        Route::get('get_history', 'TripsController@getHistory');
        Route::post('update_trip_request/{id}', 'TripsController@updateTripRequest');
        Route::post('search', 'SearchController@search');
        Route::post('rating', 'TripsController@rating');
    });

    Route::group(['prefix' => 'notification'],function(){
        Route::post('store_token', 'NotificationsController@storeToken');
        Route::post('update_token', 'NotificationsController@updateToken');
        Route::post('send_request', 'NotificationsController@sendRequest');
        Route::get('get_notifications', 'NotificationsController@getNotifications');
        Route::post('alert_guardian', 'NotificationsController@alertGuardian');
    });

    Route::group(['prefix' => 'chat'],function(){
        Route::post('send_message', 'ChatsController@storeMessage');
        Route::post('get_message', 'ChatsController@getMessage');
        Route::get('get_chat_users', 'ChatsController@getChatUsers');
    });

    Route::group(['prefix' => 'guardian'],function(){
        Route::post('update_guardian/{id}', 'GuardiansController@updateGuardian');
        Route::get('get_guardians', 'GuardiansController@getGuardians');
    });
});



