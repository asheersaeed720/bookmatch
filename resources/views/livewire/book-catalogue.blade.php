<div>
    {{-- Page header --}}
    <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold text-white">Book Catalogue</h1>
            <p class="text-indigo-300 mt-1 text-sm">
                {{ $books->total() }} {{ Str::plural('book', $books->total()) }} in the library
            </p>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                {{-- Search --}}
                <div class="relative flex-1 min-w-0">
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="search"
                        placeholder="Search by title, author, or ISBN…"
                        class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border border-gray-200 bg-gray-50
                               focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:bg-white focus:outline-none
                               transition"
                    >
                </div>

                {{-- Genre --}}
                <select
                    wire:model.live="genre"
                    class="py-2.5 pl-3 pr-8 text-sm rounded-lg border border-gray-200 bg-gray-50
                           focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none
                           text-gray-700 transition"
                >
                    <option value="">All Genres</option>
                    @foreach($genres as $g)
                        <option value="{{ $g->id }}">{{ $g->name }}</option>
                    @endforeach
                </select>

                {{-- Min Rating --}}
                <select
                    wire:model.live="minRating"
                    class="py-2.5 pl-3 pr-8 text-sm rounded-lg border border-gray-200 bg-gray-50
                           focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none
                           text-gray-700 transition"
                >
                    <option value="0">Any Rating</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}">{{ $i }}+ ★</option>
                    @endfor
                </select>

                {{-- Sort --}}
                <select
                    wire:model.live="sort"
                    class="py-2.5 pl-3 pr-8 text-sm rounded-lg border border-gray-200 bg-gray-50
                           focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none
                           text-gray-700 transition"
                >
                    <option value="newest">Newest</option>
                    <option value="title">Title A–Z</option>
                    <option value="rating">Highest Rated</option>
                </select>

                {{-- Active filters clear pill --}}
                @if($search || $genre || $minRating > 0)
                <button
                    wire:click="clearFilters"
                    class="shrink-0 inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50
                           px-3 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-100 transition-colors"
                >
                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear filters
                </button>
                @endif

                {{-- Loading indicator --}}
                <div wire:loading class="shrink-0 flex items-center gap-1.5 text-indigo-600">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="text-xs font-medium">Loading…</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if($books->isEmpty())
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <svg class="h-14 w-14 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6
                          18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3
                          .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                </svg>
                <p class="mt-4 text-lg font-semibold text-gray-900">No books found</p>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filters.</p>
                <button
                    wire:click="clearFilters"
                    class="mt-5 inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm
                           font-medium text-white hover:bg-indigo-700 transition"
                >
                    Clear filters
                </button>
            </div>

        @else
            {{-- Book grid --}}
            <div
                wire:loading.class="opacity-50 pointer-events-none"
                class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5 transition-opacity duration-150"
            >
                @foreach($books as $book)
                <a
                    href="{{ route('books.show', $book) }}"
                    class="group flex flex-col rounded-xl overflow-hidden border border-gray-100 bg-white
                           shadow-sm hover:shadow-md transition-shadow duration-200"
                >
                    {{-- Cover --}}
                    <div class="aspect-[2/3] overflow-hidden bg-gray-100">
                        @if($book->cover_image)
                            <img
                                src="{{ Storage::disk('public')->url($book->cover_image) }}"
                                alt="{{ $book->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center
                                        bg-gradient-to-br from-indigo-100 to-indigo-200">
                                <span class="text-3xl font-extrabold text-indigo-400 select-none tracking-tight">
                                    {{ collect(explode(' ', $book->title))
                                        ->map(fn ($w) => strtoupper($w[0]))
                                        ->take(2)
                                        ->implode('') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-3 flex flex-col gap-1.5 flex-1">

                        {{-- Badges row --}}
                        <div class="flex items-center justify-between gap-1 flex-wrap">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                         bg-amber-100 text-amber-800 truncate max-w-[60%]">
                                {{ $book->genre->name }}
                            </span>
                            @if($book->available_copies > 0)
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                             bg-green-100 text-green-700 shrink-0">
                                    Available
                                </span>
                            @else
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                             bg-red-100 text-red-600 shrink-0">
                                    Out of Stock
                                </span>
                            @endif
                        </div>

                        {{-- Title --}}
                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug">
                            {{ $book->title }}
                        </h3>

                        {{-- Author --}}
                        <p class="text-xs text-gray-500 truncate">{{ $book->author }}</p>

                        {{-- Star rating --}}
                        <div class="flex items-center gap-0.5 mt-auto pt-1">
                            @if($book->avg_rating !== null)
                                @for($i = 1; $i <= 5; $i++)
                                    <svg
                                        class="h-3.5 w-3.5 {{ $i <= round($book->avg_rating) ? 'text-amber-400' : 'text-gray-200' }}"
                                        fill="currentColor" viewBox="0 0 20 20"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0
                                                 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0
                                                 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54
                                                 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8
                                                 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0
                                                 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                                                 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="text-xs text-gray-400 ml-0.5">
                                    {{ number_format($book->avg_rating, 1) }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">No ratings yet</span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($books->hasPages())
            <div class="mt-8">
                {{ $books->links() }}
            </div>
            @endif

        @endif
    </div>
</div>
