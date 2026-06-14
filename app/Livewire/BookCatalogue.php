<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Book;
use App\Models\Genre;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.public')]
#[Title('Book Catalogue')]
class BookCatalogue extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $genre = '';

    #[Url(as: 'rating', except: 0)]
    public int $minRating = 0;

    #[Url(except: 'newest')]
    public string $sort = 'newest';

    public function updatingSearch(): void  { $this->resetPage(); }
    public function updatingGenre(): void   { $this->resetPage(); }
    public function updatingMinRating(): void { $this->resetPage(); }
    public function updatingSort(): void    { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search    = '';
        $this->genre     = '';
        $this->minRating = 0;
        $this->sort      = 'newest';
        $this->resetPage();
    }

    public function render()
    {
        $avgSubquery = '(SELECT COALESCE(AVG(r.rating), 0) FROM ratings r
                         WHERE r.book_id = books.id AND r.is_approved = 1)';

        $books = Book::with('genre')
            ->withAvg(
                ['ratings as avg_rating' => fn ($q) => $q->where('is_approved', true)],
                'rating'
            )
            ->when($this->search, fn ($q) => $q->where(function ($inner) {
                $inner->where('title', 'like', "%{$this->search}%")
                      ->orWhere('author', 'like', "%{$this->search}%")
                      ->orWhere('isbn', 'like', "%{$this->search}%");
            }))
            ->when($this->genre !== '', fn ($q) => $q->where('genre_id', $this->genre))
            ->when($this->minRating > 0, fn ($q) => $q->whereRaw(
                "$avgSubquery >= ?",
                [$this->minRating]
            ))
            ->when($this->sort === 'title', fn ($q) => $q->orderBy('title'))
            ->when($this->sort === 'rating', fn ($q) => $q->orderByRaw("$avgSubquery DESC"))
            ->when(! in_array($this->sort, ['title', 'rating']), fn ($q) => $q->latest())
            ->paginate(12);

        $genres = Genre::orderBy('name')->get();

        return view('livewire.book-catalogue', compact('books', 'genres'));
    }
}
