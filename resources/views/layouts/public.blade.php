<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'BookMatch') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-gray-900 min-h-screen flex flex-col">
        @include('layouts.navigation')

        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer class="mt-16 border-t border-gray-100 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 items-start">

                    {{-- Brand column --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0
                                         016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18
                                         3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                            <span class="font-bold text-sm text-gray-900">Book<span class="text-amber-600">Match</span></span>
                        </div>
                        <p class="text-xs text-gray-400 leading-relaxed">
                            University Library book discovery,<br>borrowing, and recommendation system.
                        </p>
                    </div>

                    {{-- Navigation column --}}
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-3">Explore</p>
                        <nav class="space-y-2">
                            <a href="{{ route('home') }}" class="block text-sm text-gray-500 hover:text-indigo-600 transition-colors">Home</a>
                            <a href="{{ route('books.index') }}" class="block text-sm text-gray-500 hover:text-indigo-600 transition-colors">Book Catalogue</a>
                            @auth
                            <a href="{{ route('dashboard') }}" class="block text-sm text-gray-500 hover:text-indigo-600 transition-colors">My Dashboard</a>
                            <a href="{{ route('bookmarks.index') }}" class="block text-sm text-gray-500 hover:text-indigo-600 transition-colors">My Bookmarks</a>
                            @endauth
                        </nav>
                    </div>

                    {{-- Copyright column --}}
                    <div class="sm:text-right">
                        <p class="text-xs text-gray-400">&copy; {{ date('Y') }} BookMatch.</p>
                        <p class="text-xs text-gray-400 mt-1">University Library System.</p>
                    </div>

                </div>
            </div>
        </footer>
    </body>
</html>
