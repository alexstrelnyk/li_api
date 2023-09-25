<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'devices',
    'as'         => 'device.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::post('/activate', 'DeviceController@activate')->name('activate');
    Route::post('/deactivate', 'DeviceController@deactivate')->name('deactivate');
});