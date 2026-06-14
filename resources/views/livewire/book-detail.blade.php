<div>
    {{-- Flash message --}}
    @if(session('success'))
    <div
        class="fixed top-4 right-4 z-50 max-w-sm w-full"
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 5000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl shadow-lg px-4 py-3">
            <svg class="h-5 w-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-green-800 font-medium flex-1">{{ session('success') }}</p>
            <button @click="show = false" class="text-green-400 hover:text-green-600 transition-colors shrink-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
    @endif

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-10">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-gray-600 transition-colors">Home</a>
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('books.index') }}" class="hover:text-gray-600 transition-colors">Catalogue</a>
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-600 font-medium truncate">{{ $book->title }}</span>
        </nav>

        {{-- ── Book detail card ─────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
            <div class="flex flex-col sm:flex-row gap-8 p-6 sm:p-8">

                {{-- Cover image --}}
                <div class="shrink-0 mx-auto sm:mx-0">
                    <div class="w-44 h-64 sm:w-52 rounded-xl overflow-hidden shadow-md bg-gray-100" style="height: clamp(16rem, 22vw, 22rem)">
                        @if($book->cover_image)
                            <img
                                src="{{ Storage::disk('public')->url($book->cover_image) }}"
                                alt="{{ $book->title }}"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-100 to-indigo-200">
                                <span class="text-4xl font-extrabold text-indigo-400 select-none tracking-tight">
                                    {{ collect(explode(' ', $book->title))->map(fn ($w) => strtoupper($w[0]))->take(2)->implode('') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="flex-1 min-w-0">

                    {{-- Badges --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">
                            {{ $book->genre->name }}
                        </span>
                        @if($book->available_copies > 0)
                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                                Available · {{ $book->available_copies }} {{ Str::plural('copy', $book->available_copies) }}
                            </span>
                        @else
                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-600">
                                Out of Stock
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight mb-1">
                        {{ $book->title }}
                    </h1>
                    <p class="text-sm text-gray-500 mb-4">
                        by <span class="font-medium text-gray-700">{{ $book->author }}</span>
                    </p>

                    {{-- Star rating summary --}}
                    @php
                        $avg   = $book->average_rating;
                        $count = $book->approved_ratings_count;
                    @endphp
                    <div class="flex items-center gap-2 mb-5">
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg
                                    class="h-4 w-4 {{ $i <= round($avg) ? 'text-amber-400' : 'text-gray-200' }}"
                                    fill="currentColor" viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        @if($count > 0)
                            <span class="text-sm font-semibold text-gray-800">{{ number_format($avg, 1) }}</span>
                            <span class="text-sm text-gray-400">({{ $count }} {{ Str::plural('review', $count) }})</span>
                        @else
                            <span class="text-sm text-gray-400 italic">No ratings yet</span>
                        @endif
                    </div>

                    {{-- Details grid --}}
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm mb-6">
                        @if($book->isbn)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-0.5">ISBN</dt>
                            <dd class="text-gray-700 font-mono text-xs">{{ $book->isbn }}</dd>
                        </div>
                        @endif
                        @if($book->publisher)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-0.5">Publisher</dt>
                            <dd class="text-gray-700">{{ $book->publisher }}</dd>
                        </div>
                        @endif
                        @if($book->published_year)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-0.5">Year</dt>
                            <dd class="text-gray-700">{{ $book->published_year }}</dd>
                        </div>
                        @endif
                        @if($book->location_code)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-0.5">Location</dt>
                            <dd class="text-gray-700 font-mono">{{ $book->location_code }}</dd>
                        </div>
                        @endif
                    </dl>

                    {{-- Action buttons --}}
                    <div class="flex flex-wrap items-center gap-3">
                        @auth
                            @if($book->available_copies > 0)
                            <button
                                wire:click="borrowBook"
                                wire:loading.attr="disabled"
                                wire:target="borrowBook"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold
                                       text-white shadow-sm hover:bg-indigo-700 active:bg-indigo-800 transition-colors
                                       disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                <svg wire:loading.remove wire:target="borrowBook"
                                     class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016
                                             18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3
                                             .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                </svg>
                                <svg wire:loading wire:target="borrowBook"
                                     class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Borrow Book
                            </button>
                            @endif

                            <button
                                wire:click="toggleBookmark"
                                wire:loading.attr="disabled"
                                wire:target="toggleBookmark"
                                class="inline-flex items-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold
                                       transition-colors disabled:opacity-60
                                       {{ $isBookmarked
                                           ? 'border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100'
                                           : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="{{ $isBookmarked ? 'currentColor' : 'none' }}"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0
                                             00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                {{ $isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                            </button>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm
                                       font-semibold text-white hover:bg-indigo-700 transition-colors"
                            >
                                Login to Borrow
                            </a>
                        @endauth
                    </div>

                    @error('borrow')
                    <p class="mt-2 text-sm text-red-600 flex items-center gap-1.5">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732
                                     4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            @if($book->description)
            <div class="px-6 sm:px-8 pb-6 sm:pb-8 border-t border-gray-50 pt-5">
                <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Description</h2>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $book->description }}</p>
            </div>
            @endif
        </div>

        {{-- ── Reviews ──────────────────────────────────────────── --}}
        <div class="mb-10">
            <h2 class="text-xl font-bold text-gray-900 mb-5">
                Reviews
                @if($approvedRatings->isNotEmpty())
                    <span class="text-base font-normal text-gray-400">({{ $approvedRatings->count() }})</span>
                @endif
            </h2>

            @if($approvedRatings->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-2xl border border-gray-100">
                    <svg class="h-10 w-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                         stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166
                                 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0
                                 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626
                                 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0
                                 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No approved reviews yet. Be the first!</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($approvedRatings as $review)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-indigo-600">
                                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $review->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $review->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5 shrink-0">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg
                                        class="h-4 w-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                        fill="currentColor" viewBox="0 0 20 20"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        @if($review->message)
                        <p class="mt-3 text-sm text-gray-600 leading-relaxed">{{ $review->message }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ── Write / manage review ────────────────────────────── --}}
        @auth
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">

            @if($userRating)
                {{-- ── User already has a rating ── --}}
                <h2 class="text-lg font-bold text-gray-900 mb-4">Your Review</h2>

                @if($editingRating)
                    {{-- Inline edit form --}}
                    <form wire:submit="updateRating">
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rating</label>
                            <div x-data="{ hover: 0 }" class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <button
                                    type="button"
                                    @mouseenter="hover = {{ $i }}"
                                    @mouseleave="hover = 0"
                                    @click="$wire.set('editRating', {{ $i }})"
                                    class="p-0.5 focus:outline-none transition-transform hover:scale-110"
                                >
                                    <svg
                                        class="h-8 w-8 transition-colors"
                                        :class="(hover ? hover >= {{ $i }} : $wire.editRating >= {{ $i }}) ? 'text-amber-400' : 'text-gray-200'"
                                        fill="currentColor" viewBox="0 0 20 20"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                                @endfor
                            </div>
                            @error('editRating')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Review <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <textarea
                                wire:model="editMessage"
                                rows="3"
                                placeholder="Share your thoughts..."
                                class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm text-gray-900
                                       placeholder-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100
                                       focus:outline-none resize-none"
                            ></textarea>
                            @error('editMessage')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="updateRating"
                                class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white
                                       hover:bg-indigo-700 transition-colors disabled:opacity-60"
                            >
                                Update Review
                            </button>
                            <button
                                type="button"
                                wire:click="cancelEdit"
                                class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-semibold
                                       text-gray-600 hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>

                @else
                    {{-- Read-only view of existing rating --}}
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg
                                        class="h-5 w-5 {{ $i <= $userRating->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                        fill="currentColor" viewBox="0 0 20 20"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            @if($userRating->message)
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $userRating->message }}</p>
                            @endif
                            @if(! $userRating->is_approved)
                            <p class="mt-2 text-xs text-amber-600 inline-flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                                </svg>
                                Pending approval
                            </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button
                                wire:click="startEdit"
                                class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold
                                       text-gray-600 hover:bg-gray-50 transition-colors"
                            >
                                Edit
                            </button>
                            <button
                                wire:click="deleteRating"
                                wire:confirm="Delete your review? This cannot be undone."
                                wire:loading.attr="disabled"
                                wire:target="deleteRating"
                                class="rounded-lg border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-semibold
                                       text-red-600 hover:bg-red-100 transition-colors disabled:opacity-60"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                @endif

            @else
                {{-- ── New review form ── --}}
                <h2 class="text-lg font-bold text-gray-900 mb-4">Write a Review</h2>

                <form wire:submit="submitRating">
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Your Rating <span class="text-red-500">*</span>
                        </label>
                        <div x-data="{ hover: 0 }" class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                @mouseenter="hover = {{ $i }}"
                                @mouseleave="hover = 0"
                                @click="$wire.set('newRating', {{ $i }})"
                                class="p-1 focus:outline-none transition-transform hover:scale-110"
                            >
                                <svg
                                    class="h-9 w-9 transition-colors duration-100"
                                    :class="(hover ? hover >= {{ $i }} : $wire.newRating >= {{ $i }}) ? 'text-amber-400' : 'text-gray-200'"
                                    fill="currentColor" viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                            @endfor
                            <span
                                class="ml-2 text-sm text-gray-500 w-20"
                                x-show="$wire.newRating > 0"
                                x-text="['','Poor','Fair','Good','Very Good','Excellent'][$wire.newRating]"
                            ></span>
                        </div>
                        @error('newRating')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Review <span class="text-gray-400 font-normal">(optional)</span>
                        </label>
                        <textarea
                            wire:model="newMessage"
                            rows="4"
                            placeholder="Share your thoughts about this book…"
                            class="w-full rounded-lg border border-gray-200 px-3 py-2.5 text-sm text-gray-900
                                   placeholder-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100
                                   focus:outline-none resize-none"
                        ></textarea>
                        @error('newMessage')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="submitRating"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm
                               font-semibold text-white hover:bg-indigo-700 transition-colors
                               disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <svg wire:loading.remove wire:target="submitRating"
                             class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0
                                     1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755
                                     1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <svg wire:loading wire:target="submitRating"
                             class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Submit Review
                    </button>
                </form>
            @endif
        </div>
        @endauth

        {{-- Guest CTA --}}
        @guest
        <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-6 text-center">
            <p class="text-sm text-indigo-700 font-medium mb-3">
                <a href="{{ route('login') }}" class="underline underline-offset-2 hover:text-indigo-900">Login</a>
                to write a review or bookmark this book.
            </p>
        </div>
        @endguest

    </div>
</div>
