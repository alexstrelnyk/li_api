<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'reflections',
    'as'         => 'reflection.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::get('', 'UserReflectionController@index')->name('list');
    Route::post('', 'UserReflectionController@create')->name('create');
});
