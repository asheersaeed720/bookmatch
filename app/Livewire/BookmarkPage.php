<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
class BookmarkPage extends Component
{
    use WithPagination;

    public function removeBookmark(int $bookmarkId): void
    {
        Bookmark::where('id', $bookmarkId)
            ->where('user_id', Auth::id())
            ->delete();

        session()->flash('success', 'Bookmark removed.');
    }

    public function render()
    {
        $bookmarks = Bookmark::with([
            'book' => fn ($q) => $q
                ->with('genre')
                ->withAvg(
                    ['ratings as avg_rating' => fn ($r) => $r->where('is_approved', true)],
                    'rating'
                ),
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('livewire.bookmark-page', compact('bookmarks'))
            ->title('My Bookmarks');
    }
}
