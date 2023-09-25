<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'activity',
    'as'         => 'activity.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('/me', 'ActivityController@me')->name('me');
    Route::get('/company', 'ActivityController@company')->name('company');
    Route::get('/journal', 'ActivityController@journal')->name('journal');
    Route::get('/status', 'ActivityController@status')->name('status');
    Route::post('/viewed', 'ActivityController@viewActivities')->name('set-view');
});
