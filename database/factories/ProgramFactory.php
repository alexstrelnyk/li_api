<?php

/** @var Factory $factory */

use App\Models\Program;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Program::class, static function (Faker $faker) {
    return [
        'name' => $faker->jobTitle,
    ];
});
