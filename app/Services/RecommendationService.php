<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function generateForUser(User $user): void
    {
        Recommendation::where('user_id', $user->id)->delete();

        $ratedBookIds = $user->ratings()->pluck('book_id')->all();

        $rows = [];

        foreach ($this->collaborative($user) as $item) {
            $rows[] = $this->row($user->id, $item, 'collaborative');
        }

        foreach ($this->genreBased($user) as $item) {
            $rows[] = $this->row($user->id, $item, 'genre_based');
        }

        $insertedBookIds = array_column($rows, 'book_id');

        foreach ($this->trending() as $item) {
            if (!in_array($item['book_id'], $ratedBookIds, true) &&
                !in_array($item['book_id'], $insertedBookIds, true)) {
                $rows[] = $this->row($user->id, $item, 'trending');
                $insertedBookIds[] = $item['book_id'];
            }
        }

        if ($rows !== []) {
            Recommendation::insert($rows);
        }
    }

    public function collaborative(User $user): Collection
    {
        $userRatings = $user->ratings()->pluck('rating', 'book_id');

        if ($userRatings->isEmpty()) {
            return collect();
        }

        $ratedBookIds = $userRatings->keys()->all();

        $similarUserIds = DB::table('ratings')
            ->whereIn('book_id', $ratedBookIds)
            ->where('user_id', '!=', $user->id)
            ->where('is_approved', true)
            ->get()
            ->filter(function (object $row) use ($userRatings): bool {
                $mine = $userRatings->get($row->book_id);
                return $mine !== null && abs($row->rating - $mine) <= 1;
            })
            ->pluck('user_id')
            ->unique()
            ->values();

        if ($similarUserIds->isEmpty()) {
            return collect();
        }

        return DB::table('ratings')
            ->whereIn('user_id', $similarUserIds)
            ->whereNotIn('book_id', $ratedBookIds)
            ->where('rating', '>=', 4)
            ->where('is_approved', true)
            ->select('book_id', DB::raw('AVG(rating) as score'))
            ->groupBy('book_id')
            ->orderByDesc('score')
            ->limit(10)
            ->get()
            ->map(fn (object $row): array => ['book_id' => (int) $row->book_id, 'score' => (float) $row->score]);
    }

    public function genreBased(User $user): Collection
    {
        $ratedBookIds = $user->ratings()->pluck('book_id')->all();

        $topGenreIds = DB::table('ratings')
            ->join('books', 'ratings.book_id', '=', 'books.id')
            ->where('ratings.user_id', $user->id)
            ->select('books.genre_id', DB::raw('AVG(ratings.rating) as avg_rating'))
            ->groupBy('books.genre_id')
            ->orderByDesc('avg_rating')
            ->limit(3)
            ->pluck('genre_id');

        if ($topGenreIds->isEmpty()) {
            return collect();
        }

        $excludeIds = $ratedBookIds ?: [0];

        return DB::table('books')
            ->join('ratings', 'books.id', '=', 'ratings.book_id')
            ->whereIn('books.genre_id', $topGenreIds)
            ->whereNotIn('books.id', $excludeIds)
            ->where('ratings.is_approved', true)
            ->select('books.id as book_id', DB::raw('AVG(ratings.rating) as score'))
            ->groupBy('books.id')
            ->orderByDesc('score')
            ->limit(10)
            ->get()
            ->map(fn (object $row): array => ['book_id' => (int) $row->book_id, 'score' => (float) $row->score]);
    }

    public function trending(): Collection
    {
        return DB::table('ratings')
            ->where('rating', '>=', 4)
            ->where('is_approved', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->select('book_id', DB::raw('COUNT(*) as score'))
            ->groupBy('book_id')
            ->orderByDesc('score')
            ->limit(10)
            ->get()
            ->map(fn (object $row): array => ['book_id' => (int) $row->book_id, 'score' => (float) $row->score]);
    }

    /** @param array{book_id: int, score: float} $item */
    private function row(int $userId, array $item, string $reasonType): array
    {
        return [
            'user_id'     => $userId,
            'book_id'     => $item['book_id'],
            'score'       => $item['score'],
            'reason_type' => $reasonType,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }
}
