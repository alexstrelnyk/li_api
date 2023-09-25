<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'topic-areas',
    'as'         => 'topic-area.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'TopicAreaController@index')->name('list');
    Route::post('', 'TopicAreaController@create')->name('create');
    Route::group(['prefix' => '{topicArea}'], static function () {
        Route::delete('', 'TopicAreaController@remove')->name('delete');

        Route::group(['prefix' => 'content-items', 'as' => 'content-items.'], static function () {
            Route::get('', 'TopicAreaController@contentItems')->name('list');
            Route::post('add', 'TopicAreaController@attachContentItem')->name('add');
            Route::post('remove', 'TopicAreaController@detachContentItem')->name('remove');
        });
    });
});