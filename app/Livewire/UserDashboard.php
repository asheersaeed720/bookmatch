<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class UserDashboard extends Component
{
    public string $tab = 'collaborative';

    public ?int $editingRatingId = null;
    public int $editRating = 0;
    public string $editMessage = '';

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['collaborative', 'genre_based', 'trending'], true)
            ? $tab
            : 'collaborative';
    }

    public function startEdit(int $ratingId): void
    {
        $rating = Auth::user()->ratings()->findOrFail($ratingId);
        $this->editingRatingId = $ratingId;
        $this->editRating      = $rating->rating;
        $this->editMessage     = $rating->message ?? '';
    }

    public function cancelEdit(): void
    {
        $this->editingRatingId = null;
        $this->editRating      = 0;
        $this->editMessage     = '';
    }

    public function saveRatingEdit(): void
    {
        $this->validate([
            'editRating'  => ['required', 'integer', 'min:1', 'max:5'],
            'editMessage' => ['nullable', 'string', 'max:1000'],
        ]);

        /** @var Rating $rating */
        $rating = Auth::user()->ratings()->findOrFail($this->editingRatingId);

        $rating->update([
            'rating'      => $this->editRating,
            'message'     => $this->editMessage ?: null,
            'is_approved' => false,
        ]);

        $this->editingRatingId = null;
        $this->editRating      = 0;
        $this->editMessage     = '';

        session()->flash('success', 'Review updated and resubmitted for approval.');
    }

    public function deleteRating(int $ratingId): void
    {
        Auth::user()->ratings()->findOrFail($ratingId)->delete();
        session()->flash('success', 'Review deleted.');
    }

    public function render()
    {
        $user = Auth::user();

        $stats = [
            'ratings_count'    => $user->ratings()->count(),
            'borrows_count'    => $user->borrows()->count(),
            'avg_rating_given' => (float) ($user->ratings()->avg('rating') ?? 0),
        ];

        $recommendations = $user->recommendations()
            ->with('book.genre')
            ->where('reason_type', $this->tab)
            ->orderByDesc('score')
            ->take(8)
            ->get();

        $myRatings = $user->ratings()
            ->with('book')
            ->latest()
            ->get();

        $myBorrows = $user->borrows()
            ->with('book')
            ->latest()
            ->get();

        return view('livewire.user-dashboard', compact('stats', 'recommendations', 'myRatings', 'myBorrows'))
            ->title('My Dashboard');
    }
}
