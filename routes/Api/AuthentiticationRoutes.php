<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'auth',
    'as'         => 'auth.',
    'namespace'  => 'Auth',
], static function () {
    Route::post('/magic-link', 'AuthController@login')->name('login');
    Route::post('/verify/{magicLinkToken}', 'AuthController@verify')->name('verify');
    Route::post('/login', 'AuthController@loginAdmin')->name('loginAdmin');
    Route::post('/set-password/{verificationToken}', 'AuthController@setPassword')->name('set-password');
});
