<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'title', 'author', 'isbn', 'genre_id', 'publisher', 'published_year',
    'description', 'cover_image', 'total_copies', 'available_copies', 'location_code', 'slug'
])]
class Book extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Book $book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });
    }

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) ($this->ratings()->where('is_approved', true)->avg('rating') ?? 0),
        );
    }

    protected function approvedRatingsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ratings()->where('is_approved', true)->count(),
        );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
