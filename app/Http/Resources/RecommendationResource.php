<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Recommendation
 */
class RecommendationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'score'       => (float) $this->score,
            'reason_type' => $this->reason_type,
            'book'        => new BookResource($this->whenLoaded('book')),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
