<?php

use Illuminate\Support\Facades\Route;

Route::
        namespace('App\Http\Controllers\API')->group(function () {

            // Public
            Route::post('register', 'AuthenticationController@register');
            Route::post('login', 'AuthenticationController@login');

            // Protected
            Route::middleware('auth:sanctum')->group(function () {

                Route::post('logout', 'AuthenticationController@logOut');

                /*--------- USER (role_id = 2) ---------*/
                Route::middleware('role:2')->group(function () {
                    // Route::get('profile', 'UserController@show');
                });

                /*--------- ADMIN (role_id = 1) ---------*/
                Route::middleware('role:1')->prefix('admin')->group(function () {
                    Route::get('users', 'AuthenticationController@userInfo');
                    // Route::delete('users/{id}', 'UserController@destroy');
                });
            });
        });
