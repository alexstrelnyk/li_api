<?php
declare(strict_types=1);

Route::group([
    'prefix' => 'bookmarked-content',
    'as' => 'bookmarked-content',
    'middleware' => ['auth:api', 'verified', 'active']
], static function () {
    Route::get('', 'BookmarkedContentController@index')->name('list');
    Route::post('', 'BookmarkedContentController@create')->name('create');
});