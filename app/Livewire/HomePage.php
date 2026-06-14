<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Book;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('BookMatch — Find Your Next Great Read')]
class HomePage extends Component
{
    public function render()
    {
        $trending = Book::with('genre')
            ->whereHas('ratings', fn ($q) => $q
                ->where('is_approved', true)
                ->where('created_at', '>=', now()->subDays(30))
            )
            ->withAvg(['ratings as avg_rating' => fn ($q) => $q
                ->where('is_approved', true)
                ->where('created_at', '>=', now()->subDays(30))
            ], 'rating')
            ->orderByDesc('avg_rating')
            ->take(5)
            ->get();

        $newArrivals = Book::with('genre')
            ->latest()
            ->take(6)
            ->get();

        return view('livewire.home-page', compact('trending', 'newArrivals'));
    }
}
