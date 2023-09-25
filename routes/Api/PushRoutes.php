<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'push',
    'as'         => 'push.',
], static function () {
    Route::post('test', 'PushController@test')->name('test');
    Route::post('action', 'PushController@action')->name('action');
});
