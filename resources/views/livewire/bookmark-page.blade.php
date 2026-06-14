<div>
    {{-- Flash --}}
    @if(session('success'))
    <div
        class="fixed top-4 right-4 z-50 max-w-sm w-full"
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 4000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl shadow-lg px-4 py-3">
            <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-green-800 font-medium flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    {{-- Page header --}}
    <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold text-white">My Bookmarks</h1>
            <p class="text-indigo-300 mt-1 text-sm">
                {{ $bookmarks->total() }} {{ Str::plural('book', $bookmarks->total()) }} saved
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        @if($bookmarks->isEmpty())
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center py-24 text-center">
                <div class="h-20 w-20 rounded-2xl bg-rose-50 flex items-center justify-center mb-5">
                    <svg class="h-10 w-10 text-rose-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0
                                 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900 mb-1">No bookmarks yet</h2>
                <p class="text-sm text-gray-500 max-w-xs">
                    Save books you want to read later by tapping the heart icon on any book page.
                </p>
                <a
                    href="{{ route('books.index') }}"
                    class="mt-6 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm
                           font-semibold text-white hover:bg-indigo-700 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    Browse catalogue
                </a>
            </div>

        @else
            {{-- Book grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
                @foreach($bookmarks as $bookmark)
                @php $book = $bookmark->book; @endphp
                <div class="group relative flex flex-col rounded-xl overflow-hidden border border-gray-100 bg-white shadow-sm hover:shadow-md transition-shadow duration-200">

                    {{-- Remove button — appears on hover --}}
                    <button
                        wire:click="removeBookmark({{ $bookmark->id }})"
                        wire:loading.attr="disabled"
                        wire:target="removeBookmark({{ $bookmark->id }})"
                        wire:confirm="Remove this bookmark?"
                        title="Remove bookmark"
                        class="absolute top-2 right-2 z-10 h-7 w-7 rounded-full bg-white/90 shadow-sm
                               flex items-center justify-center text-gray-400
                               hover:text-red-500 hover:bg-white hover:shadow
                               opacity-0 group-hover:opacity-100 focus:opacity-100
                               transition-all duration-150 disabled:opacity-40"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    {{-- Cover --}}
                    <a href="{{ route('books.show', $book) }}" class="block aspect-[2/3] overflow-hidden bg-gray-100 shrink-0">
                        @if($book->cover_image)
                            <img
                                src="{{ Storage::url($book->cover_image) }}"
                                alt="{{ $book->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-100 to-indigo-200">
                                <span class="text-3xl font-extrabold text-indigo-400 select-none tracking-tight">
                                    {{ collect(explode(' ', $book->title))->map(fn ($w) => strtoupper($w[0]))->take(2)->implode('') }}
                                </span>
                            </div>
                        @endif
                    </a>

                    {{-- Info --}}
                    <div class="p-3 flex flex-col gap-1.5 flex-1">

                        {{-- Genre badge --}}
                        @if($book->genre)
                        <span class="self-start text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 truncate max-w-full">
                            {{ $book->genre->name }}
                        </span>
                        @endif

                        {{-- Title --}}
                        <a
                            href="{{ route('books.show', $book) }}"
                            class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug hover:text-indigo-600 transition-colors"
                        >
                            {{ $book->title }}
                        </a>

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
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="text-xs text-gray-400 ml-0.5">{{ number_format($book->avg_rating, 1) }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">No ratings yet</span>
                            @endif
                        </div>

                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($bookmarks->hasPages())
            <div class="mt-8">
                {{ $bookmarks->links() }}
            </div>
            @endif

        @endif
    </div>
</div>
