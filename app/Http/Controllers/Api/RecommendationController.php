<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecommendationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecommendationController extends Controller
{
    /**
     * Personalised recommendations for the authenticated user, filtered by
     * reason type. Mirrors the UserDashboard recommendation tabs.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $type = (string) $request->query('type', 'collaborative');

        if (! in_array($type, ['collaborative', 'genre_based', 'trending'], true)) {
            $type = 'collaborative';
        }

        $recommendations = $request->user()->recommendations()
            ->where('reason_type', $type)
            ->with(['book' => fn ($q) => $q
                ->with('genre')
                ->withAvg(['ratings as avg_rating' => fn ($r) => $r->where('is_approved', true)], 'rating')
                ->withCount(['ratings as approved_count' => fn ($r) => $r->where('is_approved', true)]),
            ])
            ->orderByDesc('score')
            ->get();

        return RecommendationResource::collection($recommendations);
    }
}
