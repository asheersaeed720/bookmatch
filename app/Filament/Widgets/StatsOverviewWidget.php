<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\BorrowStatus;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Rating;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Books', Book::count())
                ->description('In library')
                ->descriptionIcon('heroicon-o-book-open')
                ->chart([7, 12, 8, 13, 15, 20, 100])
                ->color('success'),
            Stat::make('Total Users', User::count())
                ->description('Registered members')
                ->descriptionIcon('heroicon-o-user-group')
                ->chart([15, 20, 25, 23, 30, 35, 23])
                ->color('primary'),
            Stat::make('Ratings This Month', Rating::whereMonth('created_at', now()->month)->count())
                ->description('Since ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-o-star')
                ->chart([5, 10, 8, 15, 12, 20, 300])
                ->color('warning'),
            Stat::make('Active Borrows', Borrow::where('status', BorrowStatus::Active)->count())
                ->description('Currently checked out')
                ->descriptionIcon('heroicon-o-arrow-right-end-on-rectangle')
                ->chart([3, 5, 4, 6, 8, 7, 0])
                ->color('danger'),
        ];
    }
}
