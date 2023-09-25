<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'admins',
    'as'         => 'admin.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'AdminController@index')->name('list');
    Route::post('', 'AdminController@create')->name('create');
    Route::group(['prefix' => '{admin}'], static function() {
        Route::put('', 'AdminController@update')->name('update');
        Route::delete('', 'AdminController@delete')->name('delete');
    });
});
