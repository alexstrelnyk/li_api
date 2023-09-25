<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'global-settings',
    'as'         => 'global-settings.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'GlobalSettingsController@getGlobalSettings')->name('getGlobalSettings');
    Route::patch('', 'GlobalSettingsController@updateGlobalSettings')->name('updateGlobalSettings');
});
