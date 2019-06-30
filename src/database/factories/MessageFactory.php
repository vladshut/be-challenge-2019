<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\Message::class, function (Faker $faker) {
    return [
        'message' => $faker->text,
        'creator_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'room_id' => function () {
            return factory(App\Room::class)->create()->id;
        },
    ];
});
