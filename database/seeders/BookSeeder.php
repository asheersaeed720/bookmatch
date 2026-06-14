<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $genres = Genre::all();

        foreach (range(1, 100) as $i) {
            $title = fake()->words(3, true);
            $slugBase = Str::slug($title);
            $slug = $slugBase;
            $counter = 1;

            while (Book::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $counter;
                $counter++;
            }

            $totalCopies = fake()->numberBetween(1, 5);

            Book::create([
                'title' => ucwords($title),
                'author' => fake()->name(),
                'isbn' => fake()->unique()->numerify('#############'),
                'genre_id' => $genres->random()->id,
                'publisher' => fake()->company(),
                'published_year' => fake()->numberBetween(1980, 2024),
                'description' => fake()->paragraph(),
                'cover_image' => fake()->boolean(70) ? 'covers/placeholder.jpg' : null,
                'total_copies' => $totalCopies,
                'available_copies' => $totalCopies,
                'location_code' => strtoupper(fake()->bothify('??-###')),
                'slug' => $slug,
            ]);
        }
    }
}
