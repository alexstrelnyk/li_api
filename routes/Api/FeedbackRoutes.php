<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'feedback',
    'as'         => 'feedback.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'FeedbackController@index')->name('list');
    Route::post('', 'FeedbackController@create')->name('create');
});
