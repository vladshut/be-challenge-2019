<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Room::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'creator_id' => function () {
            return factory(App\User::class)->create()->id;
        },
    ];
});
