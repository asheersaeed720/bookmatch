<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\BorrowStatus;
use App\Events\BookBorrowed;
use App\Http\Controllers\Controller;
use App\Http\Resources\BorrowResource;
use App\Models\Book;
use App\Models\Borrow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BorrowController extends Controller
{
    /**
     * The authenticated user's borrow history.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $borrows = $request->user()->borrows()
            ->with('book.genre')
            ->latest()
            ->paginate(15);

        return BorrowResource::collection($borrows);
    }

    /**
     * Borrow a book. Mirrors BookDetail::borrowBook():
     * 14-day due date, active status, decrement availability, fire event.
     */
    public function store(Request $request, Book $book): JsonResponse
    {
        $book->refresh();

        if ($book->available_copies <= 0) {
            return response()->json([
                'message' => 'No copies are currently available.',
            ], 422);
        }

        $borrow = Borrow::create([
            'user_id'     => $request->user()->id,
            'book_id'     => $book->id,
            'borrowed_at' => now(),
            'due_date'    => now()->addDays(14),
            'status'      => BorrowStatus::Active,
        ]);

        $book->decrement('available_copies');

        BookBorrowed::dispatch($borrow);

        return (new BorrowResource($borrow->load('book.genre')))
            ->response()
            ->setStatusCode(201);
    }
}
