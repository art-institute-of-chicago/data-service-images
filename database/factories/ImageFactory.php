<?php

$factory->define(App\Image::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->uuid,
        'title' => ucfirst( $faker->words(3, true) ),
    ];
});
