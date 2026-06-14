<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Borrow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Borrow
 */
class BorrowResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'status'      => $this->status->value,
            'is_overdue'  => $this->is_overdue,
            'borrowed_at' => $this->borrowed_at?->toIso8601String(),
            'due_date'    => $this->due_date?->toDateString(),
            'returned_at' => $this->returned_at?->toIso8601String(),
            'book'        => new BookResource($this->whenLoaded('book')),
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}
