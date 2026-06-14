<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Genre>
 */
class GenreFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name'        => Str::title($name),
            'slug'        => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 1_000_000),
            'description' => fake()->sentence(),
        ];
    }
}
