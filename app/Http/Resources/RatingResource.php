<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Rating
 */
class RatingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'rating'      => $this->rating,
            'message'     => $this->message,
            'is_approved' => $this->is_approved,
            'user'        => $this->whenLoaded('user', fn (): array => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
            'book'        => new BookResource($this->whenLoaded('book')),
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),
        ];
    }
}
