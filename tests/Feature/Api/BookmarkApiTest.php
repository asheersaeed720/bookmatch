<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggling_adds_then_removes_a_bookmark(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAsApi($user)
            ->postJson('/api/v1/books/'.$book->slug.'/bookmark')
            ->assertCreated()
            ->assertJsonPath('bookmarked', true);

        $this->assertDatabaseHas('bookmarks', ['user_id' => $user->id, 'book_id' => $book->id]);

        $this->actingAsApi($user)
            ->postJson('/api/v1/books/'.$book->slug.'/bookmark')
            ->assertOk()
            ->assertJsonPath('bookmarked', false);

        $this->assertDatabaseMissing('bookmarks', ['user_id' => $user->id, 'book_id' => $book->id]);
    }

    public function test_a_user_can_list_their_bookmarks(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->actingAsApi($user)->postJson('/api/v1/books/'.$book->slug.'/bookmark')->assertCreated();

        $this->actingAsApi($user)
            ->getJson('/api/v1/bookmarks')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.book.id', $book->id);
    }
}
