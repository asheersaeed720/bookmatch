<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_catalogue_is_publicly_listable(): void
    {
        Book::factory()->count(3)->create();

        $this->getJson('/api/v1/books')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'title', 'author', 'slug', 'available_copies', 'average_rating', 'approved_ratings_count']],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_books_can_be_searched_by_title(): void
    {
        Book::factory()->create(['title' => 'Deep Learning Foundations']);
        Book::factory()->create(['title' => 'Cooking For Beginners']);

        $this->getJson('/api/v1/books?q=Deep Learning')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Deep Learning Foundations');
    }

    public function test_books_can_be_filtered_by_genre(): void
    {
        $genreA = Genre::factory()->create();
        $genreB = Genre::factory()->create();
        Book::factory()->count(2)->create(['genre_id' => $genreA->id]);
        Book::factory()->create(['genre_id' => $genreB->id]);

        $this->getJson('/api/v1/books?genre='.$genreA->id)
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_book_detail_exposes_approved_rating_aggregates(): void
    {
        $book = Book::factory()->create();
        $users = User::factory()->count(2)->create();

        Rating::create(['user_id' => $users[0]->id, 'book_id' => $book->id, 'rating' => 4, 'is_approved' => true]);
        Rating::create(['user_id' => $users[1]->id, 'book_id' => $book->id, 'rating' => 2, 'is_approved' => false]);

        $this->getJson('/api/v1/books/'.$book->slug)
            ->assertOk()
            ->assertJsonPath('data.slug', $book->slug)
            ->assertJsonPath('data.average_rating', 4.0)
            ->assertJsonPath('data.approved_ratings_count', 1);
    }

    public function test_public_ratings_endpoint_only_returns_approved(): void
    {
        $book = Book::factory()->create();
        $users = User::factory()->count(2)->create();

        Rating::create(['user_id' => $users[0]->id, 'book_id' => $book->id, 'rating' => 5, 'is_approved' => true]);
        Rating::create(['user_id' => $users[1]->id, 'book_id' => $book->id, 'rating' => 1, 'is_approved' => false]);

        $this->getJson('/api/v1/books/'.$book->slug.'/ratings')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.rating', 5);
    }
}
