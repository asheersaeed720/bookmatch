<?php

namespace Tests\Feature\Api;

use App\Events\RatingSubmitted;
use App\Models\Book;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RatingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_a_rating_stores_it_as_unapproved(): void
    {
        Event::fake([RatingSubmitted::class]);

        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAsApi($user)
            ->postJson('/api/v1/books/'.$book->slug.'/ratings', [
                'rating'  => 5,
                'message' => 'Excellent read.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.is_approved', false);

        $this->assertDatabaseHas('ratings', [
            'user_id'     => $user->id,
            'book_id'     => $book->id,
            'rating'      => 5,
            'is_approved' => false,
        ]);

        // Not visible on the public (approved-only) endpoint yet.
        $this->getJson('/api/v1/books/'.$book->slug.'/ratings')
            ->assertOk()
            ->assertJsonCount(0, 'data');

        Event::assertDispatched(RatingSubmitted::class);
    }

    public function test_resubmitting_updates_the_existing_rating_without_duplicating(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAsApi($user)->postJson('/api/v1/books/'.$book->slug.'/ratings', ['rating' => 2])->assertCreated();
        $this->actingAsApi($user)->postJson('/api/v1/books/'.$book->slug.'/ratings', ['rating' => 4])->assertOk();

        $this->assertDatabaseCount('ratings', 1);
        $this->assertDatabaseHas('ratings', ['user_id' => $user->id, 'book_id' => $book->id, 'rating' => 4]);
    }

    public function test_rating_validation_rejects_out_of_range_values(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAsApi($user)
            ->postJson('/api/v1/books/'.$book->slug.'/ratings', ['rating' => 9])
            ->assertStatus(422);
    }

    public function test_a_user_can_delete_their_own_rating(): void
    {
        $user   = User::factory()->create();
        $book   = Book::factory()->create();
        $rating = Rating::create(['user_id' => $user->id, 'book_id' => $book->id, 'rating' => 3, 'is_approved' => false]);

        $this->actingAsApi($user)
            ->deleteJson('/api/v1/ratings/'.$rating->id)
            ->assertOk();

        $this->assertDatabaseMissing('ratings', ['id' => $rating->id]);
    }

    public function test_a_user_cannot_delete_someone_elses_rating(): void
    {
        $owner  = User::factory()->create();
        $other  = User::factory()->create();
        $book   = Book::factory()->create();
        $rating = Rating::create(['user_id' => $owner->id, 'book_id' => $book->id, 'rating' => 3, 'is_approved' => true]);

        $this->actingAsApi($other)
            ->deleteJson('/api/v1/ratings/'.$rating->id)
            ->assertForbidden();

        $this->assertDatabaseHas('ratings', ['id' => $rating->id]);
    }
}
