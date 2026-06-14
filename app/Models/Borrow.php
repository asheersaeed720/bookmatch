<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BorrowStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'book_id', 'borrowed_at', 'due_date', 'returned_at', 'status'])]
class Borrow extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'borrowed_at' => 'datetime',
            'due_date' => 'date',
            'returned_at' => 'datetime',
            'status' => BorrowStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->status === BorrowStatus::Active && now()->greaterThan($this->due_date),
        );
    }
}
