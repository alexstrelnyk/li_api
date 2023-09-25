<?php

Route::group([
    'prefix'     => 'invite',
    'as'         => 'invite.',
], static function () {
    Route::group(['middleware' => ['auth:api', 'verified', 'active']], static function () {
        Route::get('', 'InviteEmailController@index')->name('list');
        Route::post('assign', 'InviteEmailController@assign')->name('assign');
        Route::post('assign-to-me', 'InviteEmailController@assignToMe')->name('assign-to-me');
    });
    Route::post('/send', 'InviteEmailController@sendInviteMagicToken')->name('send');
    Route::post('/get', 'InviteEmailController@getInviteMagicToken')->name('get');
});
