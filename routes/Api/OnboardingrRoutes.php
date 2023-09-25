<?php
declare(strict_types=1);

Route::group([
    'prefix'     => 'onboarding',
    'as'         => 'onboarding.',
    'middleware' => ['auth:api', 'verified', 'active'],
], static function () {
    Route::post('complete', 'OnboardingController@complete')->name('complete');
});
