<?php
declare(strict_types=1);

Route::group([
    'prefix' => 'content-item-feedback',
    'as' => 'content-item-feedback.',
    'middleware' => ['auth:api', 'verified', 'active']
], static function () {
    Route::post('', 'UserFeedbackController@create')->name('create');
});