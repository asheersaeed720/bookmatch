<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Computer Science',
            'Mathematics',
            'Literature',
            'History',
            'Physics',
            'Biology',
            'Engineering',
            'Philosophy',
            'Economics',
            'Arts',
        ];

        foreach ($genres as $genre) {
            Genre::create([
                'name' => $genre,
                'slug' => Str::slug($genre),
                'description' => fake()->sentence(),
            ]);
        }
    }
}
