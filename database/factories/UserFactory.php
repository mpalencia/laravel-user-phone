<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
	$email = $faker->unique()->safeEmail;
    return [
        'name' => $faker->name,
        'email' => $email,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'api_token' => bcrypt($email)
    ];
});

$factory->define(App\Models\UserPhone::class, function (Faker\Generator $faker) {
    return [
        'user_id' => $faker->randomDigit,
        'phone_number' => $faker->phoneNumber,
    ];
});

