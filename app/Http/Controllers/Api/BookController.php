<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    /**
     * Paginated, searchable, filterable book catalogue.
     *
     * Mirrors the BookCatalogue Livewire component: search across
     * title/author/isbn, genre filter, minimum approved-rating filter, and
     * newest/title/rating sorting. Only approved ratings count toward averages.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search    = trim((string) $request->query('q', ''));
        $genre     = (string) $request->query('genre', '');
        $minRating = (int) $request->query('rating', 0);
        $sort      = (string) $request->query('sort', 'newest');

        $avgSubquery = '(SELECT COALESCE(AVG(r.rating), 0) FROM ratings r
                         WHERE r.book_id = books.id AND r.is_approved = 1)';

        $books = Book::query()
            ->with('genre')
            ->withAvg(
                ['ratings as avg_rating' => fn ($q) => $q->where('is_approved', true)],
                'rating'
            )
            ->withCount(
                ['ratings as approved_count' => fn ($q) => $q->where('is_approved', true)]
            )
            ->when($search !== '', fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            }))
            ->when($genre !== '', fn ($q) => $q->where('genre_id', $genre))
            ->when($minRating > 0, fn ($q) => $q->whereRaw("$avgSubquery >= ?", [$minRating]))
            ->when($sort === 'title', fn ($q) => $q->orderBy('title'))
            ->when($sort === 'rating', fn ($q) => $q->orderByRaw("$avgSubquery DESC"))
            ->when(! in_array($sort, ['title', 'rating'], true), fn ($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        return BookResource::collection($books);
    }

    /**
     * A single book with its approved-rating aggregates loaded.
     */
    public function show(Book $book): BookResource
    {
        $book->load('genre')
            ->loadAvg(['ratings as avg_rating' => fn ($q) => $q->where('is_approved', true)], 'rating')
            ->loadCount(['ratings as approved_count' => fn ($q) => $q->where('is_approved', true)]);

        return new BookResource($book);
    }
}
