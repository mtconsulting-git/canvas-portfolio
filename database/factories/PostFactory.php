<?php

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Canvas\Models\Post::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->uuid,
        'slug' => $faker->slug,
        'title' => [
            'it' => $faker->word,
            'en' => $faker->word,
            'es' => $faker->word,
            'fr' => $faker->word,
            'de' => $faker->word,
        ],
        'summary' => [
            'it' => $faker->word,
            'en' => $faker->word,
            'es' => $faker->word,
            'fr' => $faker->word,
            'de' => $faker->word,
        ],
        'body' => [
            'it' => $faker->realText(),
            'en' => $faker->realText(),
            'es' => $faker->realText(),
            'fr' => $faker->realText(),
            'de' => $faker->realText(),
        ],
        'published_at' => today()->toDateTimeString(),
        'featured_image' => $faker->imageUrl(),
        'featured_image_caption' => $faker->sentence,
        'gallery_images' => [],
        'user_id' => function () {
            return factory(\Canvas\Models\User::class)->create()->id;
        },
        'meta' => [
            'title' => [
                'it' => $faker->sentence,
                'en' => $faker->sentence,
                'es' => $faker->sentence,
                'fr' => $faker->sentence,
                'de' => $faker->sentence,
            ],
            'description' => [
                'it' => $faker->sentence,
                'en' => $faker->sentence,
                'es' => $faker->sentence,
                'fr' => $faker->sentence,
                'de' => $faker->sentence,
            ],
            'canonical_link' => [
                'it' => $faker->sentence,
                'en' => $faker->sentence,
                'es' => $faker->sentence,
                'fr' => $faker->sentence,
                'de' => $faker->sentence,
            ],
        ],
    ];
});
