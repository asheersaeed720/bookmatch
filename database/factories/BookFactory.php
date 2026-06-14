<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title  = Str::title(fake()->unique()->words(3, true));
        $copies = fake()->numberBetween(1, 5);

        return [
            'title'            => $title,
            'author'           => fake()->name(),
            'isbn'             => fake()->unique()->isbn13(),
            'genre_id'         => Genre::factory(),
            'publisher'        => fake()->company(),
            'published_year'   => fake()->numberBetween(1980, 2024),
            'description'      => fake()->paragraph(),
            'cover_image'      => null,
            'total_copies'     => $copies,
            'available_copies' => $copies,
            'location_code'    => 'A-'.fake()->numberBetween(1, 99),
            'slug'             => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 1_000_000),
        ];
    }
}
