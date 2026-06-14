<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Borrow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookBorrowed
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Borrow $borrow) {}
}
