<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Rating;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_stats_reviews_and_borrows(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        Rating::create(['user_id' => $user->id, 'book_id' => $book->id, 'rating' => 4, 'is_approved' => true]);

        $this->actingAsApi($user)
            ->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonStructure([
                'stats'      => ['ratings_count', 'borrows_count', 'avg_rating_given'],
                'my_ratings' => ['data'],
                'my_borrows' => ['data'],
            ])
            ->assertJsonPath('stats.ratings_count', 1)
            ->assertJsonPath('stats.avg_rating_given', 4.0);
    }

    public function test_recommendations_are_filtered_by_type(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Recommendation::create(['user_id' => $user->id, 'book_id' => $book->id, 'score' => 0.9, 'reason_type' => 'trending']);
        Recommendation::create([
            'user_id' => $user->id,
            'book_id' => Book::factory()->create()->id,
            'score'   => 0.8,
            'reason_type' => 'collaborative',
        ]);

        $this->actingAsApi($user)
            ->getJson('/api/v1/recommendations?type=trending')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.reason_type', 'trending')
            ->assertJsonPath('data.0.book.id', $book->id);
    }
}
