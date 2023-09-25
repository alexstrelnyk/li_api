<?php
declare(strict_types=1);

Route::group([
    'prefix' => 'programs',
    'as' => 'program.',
    'middleware' => ['auth:api', 'verified', 'active']
], static function () {
    Route::get('', 'ProgramController@index')->name('list');
    Route::post('', 'ProgramController@create')->name('create');
    Route::group(['prefix' => '{program}'], static function () {
        Route::put('', 'ProgramController@update')->name('update');
        Route::group(['prefix' => 'focuses', 'as' => 'focuses.'], static function () {
            Route::post('add', 'ProgramController@addFocus')->name('add');
            Route::post('remove', 'ProgramController@removeFocus')->name('remove');
        });
        Route::group(['prefix' => 'focus-areas', 'as' => 'focus-areas.'], static function () {
            Route::get('', 'ProgramController@focusAreas')->name('list');
        });
    });
});
