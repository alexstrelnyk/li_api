<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'users',
    'as'         => 'user.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'UserController@index')->name('list');
    Route::post('', 'UserController@create')->name('create');
    Route::post('import', 'UserController@import')->name('import');
    Route::group(['prefix' => '{user}'], static function () {
        Route::get('', 'UserController@show')->name('show');
        Route::put('', 'UserController@edit')->name('edit');
        Route::delete('', 'UserController@remove')->name('delete');
        Route::get('reflections', 'UserController@reflections')->name('reflections');
    });
});
