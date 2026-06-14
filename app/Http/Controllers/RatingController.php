<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, Book $book): RedirectResponse
    {
        $this->authorize('create', Rating::class);

        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $book->ratings()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [...$validated, 'user_id' => $request->user()->id, 'is_approved' => false],
        );

        return back()->with('status', 'Rating submitted and awaiting approval.');
    }

    public function destroy(Request $request, Rating $rating): RedirectResponse
    {
        $this->authorize('delete', $rating);

        $rating->delete();

        return back()->with('status', 'Rating deleted.');
    }
}
