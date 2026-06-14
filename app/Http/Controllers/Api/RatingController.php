<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Events\RatingSubmitted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRatingRequest;
use App\Http\Resources\RatingResource;
use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RatingController extends Controller
{
    /**
     * Publicly visible (approved) ratings for a book.
     */
    public function index(Book $book): AnonymousResourceCollection
    {
        $ratings = $book->ratings()
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate(15);

        return RatingResource::collection($ratings);
    }

    /**
     * Create or update the authenticated user's rating for a book.
     *
     * Mirrors BookDetail::submitRating()/updateRating(): always resets the
     * rating to unapproved (pending staff approval) and fires RatingSubmitted.
     */
    public function store(StoreRatingRequest $request, Book $book): JsonResponse
    {
        $user = $request->user();

        $rating = Rating::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            [
                'rating'      => $request->integer('rating'),
                'message'     => $request->string('message')->toString() ?: null,
                'is_approved' => false,
            ]
        );

        RatingSubmitted::dispatch($user, $book);

        return (new RatingResource($rating))
            ->additional(['message' => 'Review submitted for approval.'])
            ->response()
            ->setStatusCode($rating->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Delete the authenticated user's own rating.
     */
    public function destroy(Request $request, Rating $rating): JsonResponse
    {
        if ($rating->user_id !== $request->user()->id) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        $rating->delete();

        return response()->json(['message' => 'Review deleted.']);
    }
}
