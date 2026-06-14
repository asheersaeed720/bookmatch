<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RatingResource\Pages;
use App\Models\Rating;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RatingResource extends Resource
{
    protected static ?string $model = Rating::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-star';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('book_id')
                    ->relationship('book', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Rating::query()->with(['user', 'book']))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->weight('font-medium'),
                Tables\Columns\TextColumn::make('book.title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => str_repeat('\xE2\x98\x85', (int) $state) . str_repeat('\xE2\x98\xAF', 5 - (int) $state)),
                Tables\Columns\TextColumn::make('is_approved')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Approved' : 'Pending')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_approved')
                    ->options([
                        '0' => 'Pending',
                        '1' => 'Approved',
                    ])
                    ->query(fn (Builder $query, array $data) => isset($data['is_approved']) ? $query->where('is_approved', (bool) $data['is_approved']) : $query)
                    ->native(false),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Rating $record) {
                        DB::transaction(function () use ($record) {
                            $record->update(['is_approved' => true]);
                        });
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Rating $record) {
                        DB::transaction(function () use ($record) {
                            $record->delete();
                        });
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approveAll')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            DB::transaction(function () use ($records) {
                                $records->each->update(['is_approved' => true]);
                            });
                        }),
                    BulkAction::make('rejectAll')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            DB::transaction(function () use ($records) {
                                $records->each->delete();
                            });
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRatings::route('/'),
            'create' => Pages\CreateRating::route('/create'),
            'view' => Pages\ViewRating::route('/{record}'),
            'edit' => Pages\EditRating::route('/{record}/edit'),
        ];
    }
}
