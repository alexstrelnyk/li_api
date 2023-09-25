<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'tips',
    'as'         => 'tips.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::group(['prefix' => '{contentItem}'], static function () {
        Route::get('', 'TipController@show')->name('show');
    });
});
