<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Topic;
use Faker\Generator as Faker;

$factory->define(Topic::class, static function (Faker $faker) {
    return [
        'title' => $faker->title,
        'slug' => $faker->slug,
        'status' => $faker->randomElement([Topic::STATUS_DRAFT, Topic::STATUS_REVIEW, Topic::STATUS_PUBLISHED]),
        'introduction' => $faker->text,
        'has_practice' => $faker->boolean,
        'calendar_prompt_text' => $faker->text,
        'info_image' => $faker->imageUrl()
    ];
});
