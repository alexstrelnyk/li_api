<?php

Route::group([
    'prefix'     => 'sil-scores',
    'as'         => 'sil-score.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::post('', 'SilScoreController@addEvent')->name('addEvent');
});