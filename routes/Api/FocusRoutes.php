<?php

Route::group([
    'prefix'     => 'focuses',
    'as'         => 'focus.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'FocusController@index')->name('list');
    Route::post('', 'FocusController@create')->name('create');
    Route::group(['prefix' => '{focus}'], static function () {
        Route::get('', 'FocusController@show')->name('show');
        Route::put('', 'FocusController@update')->name('update');
        Route::patch('', 'FocusController@updatePatch')->name('update-patch');
        Route::delete('', 'FocusController@delete')->name('delete');

        Route::post('reset', 'FocusController@reset')->name('reset');
        Route::get('topics', 'FocusController@topics')->name('topics');
        Route::get('content-items', 'FocusController@contentItems')->name('content-items');
    });
});
