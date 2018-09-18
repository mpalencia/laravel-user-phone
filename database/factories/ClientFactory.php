<?php

use Faker\Generator as Faker;
use App\Models\Client;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(Client::class, function (Faker $faker) {
	$email = $faker->unique()->safeEmail;
    return [
        'name' => $faker->name,
        'email' => $email,
        'authorize' => 1,
        'password' => bcrypt(str_random(10)), // secret
        'api_token' => bcrypt($email)
    ];
});