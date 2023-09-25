<?php
declare(strict_types=1);

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ScheduleTopic;
use Faker\Generator as Faker;

$factory->define(ScheduleTopic::class, static function (Faker $faker) {
    return [
        'occurs_at' => $faker->dateTime
    ];
});
