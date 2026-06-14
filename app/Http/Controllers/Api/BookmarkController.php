<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookmarkResource;
use App\Models\Book;
use App\Models\Bookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookmarkController extends Controller
{
    /**
     * The authenticated user's bookmarked books.
     * Mirrors the BookmarkPage Livewire component.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $bookmarks = $request->user()->bookmarks()
            ->with(['book' => fn ($q) => $q
                ->with('genre')
                ->withAvg(['ratings as avg_rating' => fn ($r) => $r->where('is_approved', true)], 'rating')
                ->withCount(['ratings as approved_count' => fn ($r) => $r->where('is_approved', true)]),
            ])
            ->latest()
            ->paginate(12);

        return BookmarkResource::collection($bookmarks);
    }

    /**
     * Toggle a bookmark for the given book.
     * Mirrors BookDetail::toggleBookmark().
     */
    public function toggle(Request $request, Book $book): JsonResponse
    {
        $existing = Bookmark::where('user_id', $request->user()->id)
            ->where('book_id', $book->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return response()->json(['bookmarked' => false]);
        }

        Bookmark::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
        ]);

        return response()->json(['bookmarked' => true], 201);
    }
}
