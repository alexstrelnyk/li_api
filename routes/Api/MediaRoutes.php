<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'media',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::post('upload', 'MediaController@uploadFile')->name('upload');
});