<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Rating;
use App\Models\Genre;
use Filament\Widgets\ChartWidget;

class BarChartWidget extends ChartWidget
{
    protected ?string $heading = 'Ratings Per Genre';

    public function getData(): array
    {
        $ratingsByGenre = Rating::selectRaw('genre_id, COUNT(*) as count')
            ->join('books', 'ratings.book_id', '=', 'books.id')
            ->where('is_approved', true)
            ->groupBy('genre_id')
            ->orderByDesc('count')
            ->get();

        $genres = Genre::whereIn('id', $ratingsByGenre->pluck('genre_id'))->pluck('name', 'id');

        $labels = $ratingsByGenre->map(fn ($item) => $genres[$item->genre_id] ?? 'Unknown')->toArray();
        $counts = $ratingsByGenre->pluck('count')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Approved Ratings',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#f59e0b', '#ef4444', '#3b82f6', '#10b981', '#8b5cf6',
                        '#ec4899', '#06b6d4', '#f97316', '#6366f1', '#84cc16',
                    ],
                    'borderColor' => [
                        '#d97706', '#dc2626', '#2563eb', '#059669', '#7c3aed',
                        '#db2777', '#0891b2', '#ea580c', '#4f46e5', '#65a30d',
                    ],
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
