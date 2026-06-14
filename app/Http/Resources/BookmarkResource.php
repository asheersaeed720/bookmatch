<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Bookmark
 */
class BookmarkResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'book'       => new BookResource($this->whenLoaded('book')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
