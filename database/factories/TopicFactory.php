<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Canvas\Models\Topic::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->uuid,
        'slug' => $faker->slug,
        'name' => [
            'it' => $faker->word,
            'en' => $faker->word,
            'es' => $faker->word,
            'fr' => $faker->word,
            'de' => $faker->word,
        ],
        'user_id' => function () {
            return factory(\Canvas\Models\User::class)->create()->id;
        },
    ];
});
