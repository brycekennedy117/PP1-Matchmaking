<?php

use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => Hash::make('password'), // password
        'remember_token' => Str::random(10),
    ];
});

$factory->afterCreating(App\User::class, function ($user, $faker) {
    $email = $user['email'];
    $user->save();
    $id = $user->getID();
    $attr = factory(App\MingleLibrary\Models\UserAttributes::class)->make(['user_id' => $id,
        'gender' => $faker->randomElement($array = array ('M', 'F')),
        'interested_in' => $faker->randomElement($array = array ('M', 'F'))
    ]);
});

$factory->afterMaking(App\User::class, function ($user, $faker) {
});


