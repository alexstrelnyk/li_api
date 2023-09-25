<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\AboutMeSettings;


$factory->define(AboutMeSettings::class, static function (Faker $faker) {
    return [
        'event_practice_email' => $faker->boolean,
        'event_practice_notification' => $faker->boolean,
        'random_acts_email' => $faker->boolean,
        'random_acts_notification' => $faker->boolean,
        'login_streak_email' => $faker->boolean,
        'login_streak_notification' => $faker->boolean
    ];
});