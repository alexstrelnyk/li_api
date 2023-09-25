<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'content-items',
    'as'         => 'content-item.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'ContentItemController@index')->name('list');
    Route::post('', 'ContentItemController@create')->name('create');
    Route::group(['prefix' => '{contentItem}'], static function () {
        Route::get('', 'ContentItemController@show')->name('show');
        Route::delete('', 'ContentItemController@delete')->name('delete');
        Route::put('', 'ContentItemController@update')->name('update');
        Route::post('complete', 'ContentItemController@complete')->name('complete');
        Route::post('reflected', 'ContentItemController@reflected')->name('viewed');
        Route::post('start', 'ContentItemController@start')->name('start');
        Route::post('watched', 'ContentItemController@watched')->name('watched');
        Route::post('reset', 'ContentItemController@reset')->name('reset');
        Route::post('like', 'ContentItemController@like')->name('like');
        Route::post('dislike', 'ContentItemController@dislike')->name('dislike');
    });
});
