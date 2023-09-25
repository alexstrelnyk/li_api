<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'focus-areas',
    'as'         => 'focus-area.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'FocusAreaController@index')->name('list');
    Route::post('', 'FocusAreaController@create')->name('create');
    Route::group(['prefix' => '{focusArea}'], static function () {
        Route::get('', 'FocusAreaController@show')->name('show');
        Route::put('', 'FocusAreaController@update')->name('update');
        Route::delete('', 'FocusAreaController@remove')->name('delete');
        Route::get('topic-areas', 'FocusAreaController@topicAreas')->name('topic-areas');
    });
});
