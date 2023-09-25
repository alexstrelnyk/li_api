<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Focus;
use Faker\Generator as Faker;

$factory->define(Focus::class, static function (Faker $faker) {
    return [
        'title' => $faker->text(25),
        'slug' => $faker->slug,
        'color' => $faker->hexColor,
        'status' => $faker->randomElement(Focus::getAvailableStatuses()),
        'practice' => $faker->boolean,
        'image_url' => $faker->imageUrl(240, 240)
    ];
});
