<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Not Found · BookMatch</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4">

    {{-- Decorative number --}}
    <p class="text-[8rem] sm:text-[12rem] font-extrabold text-gray-100 leading-none select-none -mb-6 sm:-mb-10">
        404
    </p>

    {{-- Content --}}
    <div class="relative z-10 text-center max-w-md">
        <div class="h-14 w-14 rounded-2xl bg-indigo-100 flex items-center justify-center mx-auto mb-5">
            <svg class="h-7 w-7 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0
                         016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18
                         3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Page not found</h1>
        <p class="text-gray-500 text-sm mb-8">
            The book — or page — you're looking for seems to have been misplaced.
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="/"
               class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm
                      font-semibold text-white hover:bg-indigo-700 transition-colors">
                Go Home
            </a>
            <a href="/books"
               class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-2.5
                      text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                Browse Catalogue
            </a>
        </div>
    </div>

</body>
</html>
