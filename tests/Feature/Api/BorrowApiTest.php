<?php

namespace Tests\Feature\Api;

use App\Enums\BorrowStatus;
use App\Events\BookBorrowed;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BorrowApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_borrow_an_available_book(): void
    {
        Event::fake([BookBorrowed::class]);

        $user = User::factory()->create();
        $book = Book::factory()->create(['total_copies' => 2, 'available_copies' => 2]);

        $this->actingAsApi($user)
            ->postJson('/api/v1/books/'.$book->slug.'/borrow')
            ->assertCreated()
            ->assertJsonPath('data.status', BorrowStatus::Active->value);

        $this->assertDatabaseHas('borrows', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status'  => BorrowStatus::Active->value,
        ]);
        $this->assertSame(1, $book->fresh()->available_copies);

        Event::assertDispatched(BookBorrowed::class);
    }

    public function test_a_user_cannot_borrow_an_unavailable_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['total_copies' => 1, 'available_copies' => 0]);

        $this->actingAsApi($user)
            ->postJson('/api/v1/books/'.$book->slug.'/borrow')
            ->assertStatus(422);

        $this->assertDatabaseCount('borrows', 0);
        $this->assertSame(0, $book->fresh()->available_copies);
    }

    public function test_a_user_can_list_their_borrows(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['total_copies' => 3, 'available_copies' => 3]);

        $this->actingAsApi($user)->postJson('/api/v1/books/'.$book->slug.'/borrow')->assertCreated();

        $this->actingAsApi($user)
            ->getJson('/api/v1/borrows')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.book.id', $book->id);
    }
}
