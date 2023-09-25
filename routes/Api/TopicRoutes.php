<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'topics',
    'as'         => 'topic.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'TopicController@index')->name('list');
    Route::post('', 'TopicController@create')->name('create');
    Route::post('order', 'TopicController@order')->name('order');
    Route::group(['prefix' => '{topic}'], static function () {
        Route::get('', 'TopicController@show')->name('show');
        Route::get('content-items', 'TopicController@contentItems')->name('content-items');
        Route::put('', 'TopicController@update')->name('update');
        Route::patch('', 'TopicController@updatePatch')->name('update-patch');
        Route::delete('', 'TopicController@delete')->name('delete');
    });
});
