<div>
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-amber-900 py-24 sm:py-32">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-amber-400 font-semibold text-sm tracking-widest uppercase mb-3">University Library</p>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-5">
                Find your next<br>
                <span class="text-amber-400">great read</span>
            </h1>
            <p class="text-indigo-200 text-lg mb-10 max-w-xl mx-auto">
                Browse thousands of books, track your borrowing history, and discover titles tailored to your interests.
            </p>
            <form action="{{ route('books.index') }}" method="GET"
                  class="flex max-w-xl mx-auto shadow-lg rounded-xl overflow-hidden">
                <input
                    type="search"
                    name="q"
                    placeholder="Search by title, author, or ISBN…"
                    class="flex-1 px-5 py-4 text-base border-0 focus:ring-0 focus:outline-none text-gray-900 placeholder-gray-400"
                >
                <button
                    type="submit"
                    class="px-6 py-4 bg-amber-500 hover:bg-amber-400 text-white font-semibold transition-colors duration-150 whitespace-nowrap"
                >
                    Search
                </button>
            </form>
        </div>
    </section>

    {{-- Trending This Week --}}
    @if($trending->isNotEmpty())
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Trending This Week</h2>
                <p class="text-sm text-gray-500 mt-1">Highest rated in the last 30 days</p>
            </div>
            <a href="{{ route('books.index') }}" class="text-sm text-amber-600 hover:text-amber-700 font-medium shrink-0">
                View all →
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5">
            @foreach($trending as $book)
            <a href="{{ route('books.show', $book) }}"
               class="group flex flex-col rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 bg-white">
                {{-- Cover --}}
                <div class="aspect-[2/3] overflow-hidden bg-gray-100">
                    @if($book->cover_image)
                        <img
                            src="{{ Storage::url($book->cover_image) }}"
                            alt="{{ $book->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                            <span class="text-3xl font-extrabold text-gray-400 select-none">
                                {{ collect(explode(' ', $book->title))->map(fn ($w) => strtoupper($w[0]))->take(2)->implode('') }}
                            </span>
                        </div>
                    @endif
                </div>
                {{-- Info --}}
                <div class="p-3 flex flex-col gap-1 flex-1">
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 self-start max-w-full truncate">
                        {{ $book->genre->name }}
                    </span>
                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug mt-0.5">
                        {{ $book->title }}
                    </h3>
                    <p class="text-xs text-gray-500 truncate">{{ $book->author }}</p>
                    {{-- Stars --}}
                    <div class="flex items-center gap-0.5 mt-auto pt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-3.5 w-3.5 {{ $i <= round($book->avg_rating) ? 'text-amber-400' : 'text-gray-200' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <span class="text-xs text-gray-400 ml-0.5">{{ number_format($book->avg_rating, 1) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- New Arrivals --}}
    <section class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">New Arrivals</h2>
                    <p class="text-sm text-gray-500 mt-1">Recently added to the catalogue</p>
                </div>
                <a href="{{ route('books.index') }}" class="text-sm text-amber-600 hover:text-amber-700 font-medium shrink-0">
                    Browse catalogue →
                </a>
            </div>

            @if($newArrivals->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-5">
                @foreach($newArrivals as $book)
                <a href="{{ route('books.show', $book) }}"
                   class="group flex flex-col rounded-xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200 bg-white">
                    <div class="aspect-[2/3] overflow-hidden bg-gray-100">
                        @if($book->cover_image)
                            <img
                                src="{{ Storage::url($book->cover_image) }}"
                                alt="{{ $book->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-200 to-gray-300">
                                <span class="text-3xl font-extrabold text-gray-400 select-none">
                                    {{ collect(explode(' ', $book->title))->map(fn ($w) => strtoupper($w[0]))->take(2)->implode('') }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="p-3 flex flex-col gap-1 flex-1">
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 self-start max-w-full truncate">
                            {{ $book->genre->name }}
                        </span>
                        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug mt-0.5">
                            {{ $book->title }}
                        </h3>
                        <p class="text-xs text-gray-500 truncate">{{ $book->author }}</p>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-sm">No books in the catalogue yet.</p>
            @endif
        </div>
    </section>
</div>
