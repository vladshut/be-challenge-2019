<?php

/* @var $factory Factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'user_name' => $faker->userName,
        'password' => bcrypt($faker->password),
        'remember_token' => Str::random(10),
    ];
});
