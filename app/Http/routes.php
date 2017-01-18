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
	    Route::post('login','AccountController@apiPostLogin');
	     Route::post('logout', 'AccountController@apiPostLogout');
	});

//Route::group(['middleware' => 'jwt.auth'], function() {

    Route::group(['prefix' => 'accounts'],function(){
        Route::get('me', 'AccountController@apiGetAccount');
        Route::post('get_user', 'AccountController@apiGetUser');
    }); // accounts
//}); // auth   

    Route::group(['prefix' => 'trip'],function(){
        Route::post('register_trip', 'TripsController@store');
        Route::post('get_trips', 'TripsController@getTrips');
        Route::post('get_trip_details', 'TripsController@getTripDetails');
        Route::get('get_history', 'TripsController@getHistory');
    });
});



