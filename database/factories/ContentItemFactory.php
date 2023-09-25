<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ContentItem;
use Faker\Generator as Faker;

$factory->define(ContentItem::class, static function (Faker $faker) {
    return [
        'title' => $faker->text(10),
        'content_type' => $faker->randomElement(ContentItem::getAvailableContentTypes()),
        'primer_title' => $faker->text(50),
        'primer_content' => $faker->text,
        'reading_time' => random_int(1, 10),
        'info_content_image' => $faker->imageUrl(),
        'info_quick_tip' => $faker->text(100),
        'info_full_content' => $faker->text,
        'info_video_uri' => null,
        'info_source_title' => $faker->text(10),
        'info_source_link' => $faker->url,
        'has_reflection' => $faker->boolean,
        'reflection_help_text' => $faker->text(50),
        'status' => $faker->randomElement(ContentItem::getAvailableStatuses()),
    ];
});
