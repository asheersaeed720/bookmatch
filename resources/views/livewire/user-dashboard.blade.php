<div>
    {{-- Flash --}}
    @if(session('success'))
    <div
        class="fixed top-4 right-4 z-50 max-w-sm w-full"
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 5000)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
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

    {{-- Page header --}}
    <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-indigo-300 text-sm font-medium mb-1">Welcome back,</p>
            <h1 class="text-3xl font-extrabold text-white">{{ Auth::user()->name }}</h1>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">

        {{-- ── 1. Stats bar ─────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Books Rated --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $stats['ratings_count'] }}</p>
                    <p class="text-sm text-gray-500">Books Rated</p>
                </div>
            </div>

            {{-- Books Borrowed --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $stats['borrows_count'] }}</p>
                    <p class="text-sm text-gray-500">Books Borrowed</p>
                </div>
            </div>

            {{-- Avg Rating Given --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 flex items-center gap-4">
                <div class="h-11 w-11 rounded-xl bg-rose-100 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900">
                        {{ $stats['avg_rating_given'] > 0 ? number_format($stats['avg_rating_given'], 1) : '—' }}
                    </p>
                    <p class="text-sm text-gray-500">Avg Rating Given</p>
                </div>
            </div>
        </div>

        {{-- ── 2. Recommended For You ────────────────────────────── --}}
        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-4">Recommended For You</h2>

            {{-- Tabs --}}
            <div class="flex gap-1 bg-gray-100 rounded-xl p-1 w-fit mb-6">
                @foreach(['collaborative' => 'Collaborative', 'genre_based' => 'Genre-Based', 'trending' => 'Trending'] as $key => $label)
                <button
                    wire:click="setTab('{{ $key }}')"
                    class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors
                           {{ $tab === $key
                               ? 'bg-white text-indigo-700 shadow-sm'
                               : 'text-gray-500 hover:text-gray-700' }}"
                >
                    {{ $label }}
                </button>
                @endforeach
            </div>

            @if($recommendations->isEmpty())
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-2xl px-6 py-8">
                    <svg class="h-8 w-8 text-gray-300 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                    </svg>
                    <p class="text-sm text-gray-500">Recommendations are being generated daily.</p>
                </div>
            @else
                <div
                    wire:loading.class="opacity-50 pointer-events-none"
                    class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 transition-opacity duration-150"
                >
                    @foreach($recommendations as $rec)
                    <a
                        href="{{ route('books.show', $rec->book) }}"
                        class="group flex flex-col rounded-xl overflow-hidden border border-gray-100 bg-white
                               shadow-sm hover:shadow-md transition-shadow duration-200"
                    >
                        {{-- Cover --}}
                        <div class="aspect-[2/3] overflow-hidden bg-gray-100">
                            @if($rec->book->cover_image)
                                <img
                                    src="{{ Storage::url($rec->book->cover_image) }}"
                                    alt="{{ $rec->book->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-100 to-indigo-200">
                                    <span class="text-2xl font-extrabold text-indigo-400 select-none">
                                        {{ collect(explode(' ', $rec->book->title))->map(fn ($w) => strtoupper($w[0]))->take(2)->implode('') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        {{-- Info --}}
                        <div class="p-3 flex flex-col gap-1 flex-1">
                            @if($rec->book->genre)
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 self-start truncate max-w-full">
                                {{ $rec->book->genre->name }}
                            </span>
                            @endif
                            <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 leading-snug">
                                {{ $rec->book->title }}
                            </h3>
                            <p class="text-xs text-gray-500 truncate">{{ $rec->book->author }}</p>
                            <div class="mt-auto pt-1.5">
                                <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">
                                    Score: {{ number_format($rec->score, 2) }}
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ── 3. My Reviews ────────────────────────────────────── --}}
        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-4">My Reviews</h2>

            @if($myRatings->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-2xl border border-gray-100">
                    <svg class="h-10 w-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">You haven't reviewed any books yet.</p>
                    <a href="{{ route('books.index') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline font-medium">
                        Browse the catalogue →
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($myRatings as $rating)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

                        @if($editingRatingId === $rating->id)
                            {{-- ── Inline edit form ── --}}
                            <form wire:submit="saveRatingEdit" class="p-5">
                                <p class="text-sm font-semibold text-gray-800 mb-3">
                                    Editing review for
                                    <a href="{{ route('books.show', $rating->book) }}"
                                       class="text-indigo-600 hover:underline">{{ $rating->book->title }}</a>
                                </p>

                                <div class="mb-3">
                                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1.5">Rating</label>
                                    <div x-data="{ hover: 0 }" class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                        <button
                                            type="button"
                                            @mouseenter="hover = {{ $i }}"
                                            @mouseleave="hover = 0"
                                            @click="$wire.set('editRating', {{ $i }})"
                                            class="p-0.5 focus:outline-none transition-transform hover:scale-110"
                                        >
                                            <svg
                                                class="h-7 w-7 transition-colors"
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

                                <div class="mb-4">
                                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1.5">Review</label>
                                    <textarea
                                        wire:model="editMessage"
                                        rows="3"
                                        placeholder="Share your thoughts…"
                                        class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-900
                                               placeholder-gray-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100
                                               focus:outline-none resize-none"
                                    ></textarea>
                                    @error('editMessage')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex gap-2">
                                    <button
                                        type="submit"
                                        wire:loading.attr="disabled"
                                        wire:target="saveRatingEdit"
                                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white
                                               hover:bg-indigo-700 transition-colors disabled:opacity-60"
                                    >
                                        Save
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="cancelEdit"
                                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold
                                               text-gray-600 hover:bg-gray-50 transition-colors"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>

                        @else
                            {{-- ── Read view ── --}}
                            <div class="p-5 flex items-start gap-4">
                                <div class="flex-1 min-w-0">
                                    <a
                                        href="{{ route('books.show', $rating->book) }}"
                                        class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition-colors line-clamp-1"
                                    >
                                        {{ $rating->book->title }}
                                    </a>

                                    <div class="flex items-center gap-1.5 mt-1">
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                            <svg
                                                class="h-3.5 w-3.5 {{ $i <= $rating->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                                fill="currentColor" viewBox="0 0 20 20"
                                            >
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            @endfor
                                        </div>
                                        @if(! $rating->is_approved)
                                        <span class="text-xs text-amber-600 font-medium">· Pending approval</span>
                                        @endif
                                        <span class="text-xs text-gray-400">· {{ $rating->created_at->format('M j, Y') }}</span>
                                    </div>

                                    @if($rating->message)
                                    <p class="mt-1.5 text-sm text-gray-500 line-clamp-2">{{ $rating->message }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <button
                                        wire:click="startEdit({{ $rating->id }})"
                                        class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold
                                               text-gray-600 hover:bg-gray-50 transition-colors"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        wire:click="deleteRating({{ $rating->id }})"
                                        wire:confirm="Delete this review? This cannot be undone."
                                        wire:loading.attr="disabled"
                                        wire:target="deleteRating({{ $rating->id }})"
                                        class="rounded-lg border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-semibold
                                               text-red-600 hover:bg-red-100 transition-colors disabled:opacity-60"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endif

                    </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ── 4. My Borrows ────────────────────────────────────── --}}
        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-4">My Borrows</h2>

            @if($myBorrows->isEmpty())
                <div class="text-center py-12 bg-gray-50 rounded-2xl border border-gray-100">
                    <svg class="h-10 w-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                    <p class="text-gray-500 text-sm">You haven't borrowed any books yet.</p>
                    <a href="{{ route('books.index') }}" class="mt-3 inline-block text-sm text-indigo-600 hover:underline font-medium">
                        Browse the catalogue →
                    </a>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 bg-gray-50">
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Book</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 whitespace-nowrap">Borrowed</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 whitespace-nowrap">Due Date</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 whitespace-nowrap">Returned</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($myBorrows as $borrow)
                                @php
                                    $isOverdue = $borrow->is_overdue || $borrow->status === \App\Enums\BorrowStatus::Overdue;
                                @endphp
                                <tr class="{{ $isOverdue ? 'bg-red-50' : 'hover:bg-gray-50' }} transition-colors">
                                    <td class="px-5 py-3.5">
                                        <a
                                            href="{{ route('books.show', $borrow->book) }}"
                                            class="font-medium {{ $isOverdue ? 'text-red-700 hover:text-red-900' : 'text-gray-900 hover:text-indigo-600' }}
                                                   transition-colors line-clamp-1"
                                        >
                                            {{ $borrow->book->title }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap">
                                        {{ $borrow->borrowed_at->format('M j, Y') }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                        {{ $borrow->due_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($isOverdue)
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-700">
                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Overdue
                                            </span>
                                        @elseif($borrow->status === \App\Enums\BorrowStatus::Returned)
                                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                                                Returned
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                                                Active
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-400 whitespace-nowrap text-xs">
                                        {{ $borrow->returned_at ? $borrow->returned_at->format('M j, Y') : '—' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>

    </div>
</div>
