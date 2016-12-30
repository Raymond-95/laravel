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

//Route::get('user', 'UsersController@getUser');

Route::group(['middleware' => 'guest'], function() {
	Route::group(['prefix' => 'accounts'],function(){
		Route::post('signup','AccountController@apiPostSignUp');
	});
});

Route::group(['prefix' => 'accounts'],function(){
	Route::post('login','AccountController@apiPostLogin');
});

Route::group(['middleware' => 'jwt.auth'], function() {

        Route::group(['prefix' => 'accounts'],function(){
        	Route::post('authenticate', 'AuthController@authenticate');

            
            Route::post('logout', 'AccountController@apiPostLogout');
            Route::get('me', 'AccountController@apiGetAccount');
        }); // accounts

        Route::group(['prefix' => 'users'], function(){    
            Route::get('{id}', 'UserController@apiGetUser')->where('id', '[0-9]+');
        }); // users
}); // auth   