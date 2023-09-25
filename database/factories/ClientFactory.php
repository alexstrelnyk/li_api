<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Client;
use Faker\Generator as Faker;

$factory->define(Client::class, static function (Faker $faker) {
    return [
        'name' => $faker->company,
        'content_model' => $faker->randomElement(Client::getAvailableContentModels()),
        'primer_notice_timing' => random_int(5, 100),
        'content_notice_timing' => random_int(5, 100),
        'reflection_notice_timing' => random_int(5, 100),
    ];
});
