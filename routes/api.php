<?php

// Routes that don't require authentication
Route::post('/companies', 'CompanyController@create');
Route::post('/users/login-attempts', 'UserController@attemptLogin');
Route::post('/users/verifications', 'UserController@verifyEmail');

// Routes that require auth
Route::middleware(['auth:api'])->group(function () {
    Route::get('/users/me', 'UserController@getFromAuth');
    Route::post('/companies/{company}/customers', 'CustomerController@store')->middleware('can:actOnBehalfOf,company');
});
