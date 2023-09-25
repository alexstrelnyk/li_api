<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'clients',
    'as'         => 'client.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'ClientController@index')->name('list');
    Route::post('', 'ClientController@create')->name('create');
    Route::group(['prefix' => '{client}'], static function () {
        Route::get('', 'ClientController@show')->name('show');
        Route::put('', 'ClientController@update')->name('update');
        Route::delete('', 'ClientController@delete')->name('delete');
        Route::post('activate', 'ClientController@activate')->name('activate');
        Route::post('deactivate', 'ClientController@deactivate')->name('deactivate');
        Route::get('focuses', 'ClientController@focuses')->name('focuses');
        Route::get('users', 'ClientController@users')->name('users');
    });
});
