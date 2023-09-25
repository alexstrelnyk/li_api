<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'about',
    'as'         => 'about.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('/profile', 'AboutMeController@getProfile')->name('profile');
    Route::get('/settings', 'AboutMeController@getSettings')->name('settings');
    Route::patch('/settings', 'AboutMeController@updateSettings')->name('update-settings');
    Route::patch('/share', 'AboutMeController@toggleShareActivitySetting')->name('toggleShareActivitySetting');
    Route::get('/photo', 'AboutMeController@getPhoto')->name('photo');
    Route::post('/photo', 'AboutMeController@postPhoto')->name('photo');
    Route::get('/day-streak', 'AboutMeController@getDayStreak')->name('day-streak');
    Route::put('/profile', 'AboutMeController@update')->name('updateUser');
    Route::patch('/profile', 'AboutMeController@updatePatch')->name('updatePatchUser');
    Route::get('/bookmarked-content', 'AboutMeController@bookmarkedContent')->name('bookmarkedContentUser');
});
