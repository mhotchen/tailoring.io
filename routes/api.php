<?php

// Routes that don't require authentication
Route::post('/companies', 'CompanyController@create');
Route::post('/users/login-attempts', 'UserController@attemptLogin');
Route::post('/users/verifications', 'UserController@verifyEmail');

// Routes that require auth
Route::middleware(['auth:api'])->group(function () {
    Route::get('/users/me', 'UserController@getFromAuth');

    Route::middleware('can:actOnBehalfOf,company')
        ->prefix('companies/{company}')
        ->group(function () {
            Route::put('',                 'CompanyController@update');

            Route::get( '/customers',      'CustomerController@index');
            Route::post('/customers',      'CustomerController@post');
            Route::get( '/customers/{id}', 'CustomerController@get');
            Route::put( '/customers/{id}', 'CustomerController@put');

            Route::prefix('/customers/{customerId}/measurement-profiles')
                ->group(function () {
                    Route::post('{profileId}/commits', 'MeasurementProfileController@commit');
                });

            Route::get(   '/measurement-settings',      'MeasurementSettingController@index');
            Route::post(  '/measurement-settings',      'MeasurementSettingController@create');
            Route::put(   '/measurement-settings/{id}', 'MeasurementSettingController@put');
            Route::delete('/measurement-settings/{id}', 'MeasurementSettingController@delete');

            Route::get(   '/sample-garments',      'SampleGarmentController@index');
            Route::post(  '/sample-garments',      'SampleGarmentController@create');
            Route::put(   '/sample-garments/{id}', 'SampleGarmentController@put');
            Route::delete('/sample-garments/{id}', 'SampleGarmentController@delete');
        });
});
