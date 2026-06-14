<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BorrowResource;
use App\Http\Resources\RatingResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * The authenticated user's dashboard: stats, their reviews and borrows.
     * Mirrors the UserDashboard Livewire component.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $stats = [
            'ratings_count'    => $user->ratings()->count(),
            'borrows_count'    => $user->borrows()->count(),
            'avg_rating_given' => round((float) ($user->ratings()->avg('rating') ?? 0), 2),
        ];

        $myRatings = $user->ratings()
            ->with(['book' => fn ($q) => $q
                ->with('genre')
                ->withAvg(['ratings as avg_rating' => fn ($r) => $r->where('is_approved', true)], 'rating')
                ->withCount(['ratings as approved_count' => fn ($r) => $r->where('is_approved', true)]),
            ])
            ->latest()
            ->get();

        $myBorrows = $user->borrows()
            ->with('book.genre')
            ->latest()
            ->get();

        return response()->json([
            'stats'      => $stats,
            'my_ratings' => RatingResource::collection($myRatings),
            'my_borrows' => BorrowResource::collection($myBorrows),
        ]);
    }
}
