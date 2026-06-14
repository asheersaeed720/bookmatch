<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Book;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class TopBooksTableWidget extends BaseWidget
{
    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Book::query()
            ->select('books.*')
            ->selectSub(
                \Illuminate\Support\Facades\DB::table('ratings')
                    ->selectRaw('AVG(rating)')
                    ->whereColumn('ratings.book_id', 'books.id')
                    ->where('is_approved', true),
                'avg_rating'
            )
            ->selectSub(
                \Illuminate\Support\Facades\DB::table('ratings')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('ratings.book_id', 'books.id')
                    ->where('is_approved', true),
                'approved_ratings_count'
            )
            ->orderByDesc('avg_rating')
            ->limit(10);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('title')
                    ->weight('font-medium'),
                TextColumn::make('author'),
                TextColumn::make('avg_rating')
                    ->label('Avg Rating')
                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 1) : '-')
                    ->sortable(),
                TextColumn::make('approved_ratings_count')
                    ->label('Approved Ratings')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
