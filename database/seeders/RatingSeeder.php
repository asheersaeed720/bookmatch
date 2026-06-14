<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();
        $usedPairs = [];

        foreach (range(1, 300) as $i) {
            $userId = $users->random()->id;
            $bookId = $books->random()->id;
            $pairKey = $userId . '-' . $bookId;

            while (isset($usedPairs[$pairKey])) {
                $userId = $users->random()->id;
                $bookId = $books->random()->id;
                $pairKey = $userId . '-' . $bookId;
            }

            $usedPairs[$pairKey] = true;

            Rating::create([
                'user_id' => $userId,
                'book_id' => $bookId,
                'rating' => fake()->numberBetween(1, 5),
                'message' => fake()->boolean() ? fake()->sentence() : null,
                'is_approved' => true,
            ]);
        }
    }
}
