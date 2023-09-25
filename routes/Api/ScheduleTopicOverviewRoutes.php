<?php

Route::group([
    'prefix'     => 'schedule-topic',
    'as'         => 'schedule.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::post('', 'ScheduleTopicController@create')->name('create');
});