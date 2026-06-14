<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Book
 *
 * The `avg_rating` and `approved_count` aggregates are loaded by the controllers
 * (via withAvg/withCount or loadAvg/loadCount), counting only approved ratings —
 * mirroring the web catalogue behaviour.
 */
class BookResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'title'                 => $this->title,
            'author'                => $this->author,
            'isbn'                  => $this->isbn,
            'slug'                  => $this->slug,
            'publisher'             => $this->publisher,
            'published_year'        => $this->published_year,
            'description'           => $this->description,
            'cover_image'           => $this->cover_image,
            'total_copies'          => $this->total_copies,
            'available_copies'      => $this->available_copies,
            'location_code'         => $this->location_code,
            'is_available'          => $this->available_copies > 0,
            'average_rating'        => round((float) $this->avg_rating, 2),
            'approved_ratings_count' => (int) $this->approved_count,
            'genre'                 => new GenreResource($this->whenLoaded('genre')),
            'created_at'            => $this->created_at?->toIso8601String(),
        ];
    }
}
