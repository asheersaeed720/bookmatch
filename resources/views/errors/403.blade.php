<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Access Denied · BookMatch</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex flex-col items-center justify-center px-4">

    {{-- Decorative number --}}
    <p class="text-[8rem] sm:text-[12rem] font-extrabold text-gray-100 leading-none select-none -mb-6 sm:-mb-10">
        403
    </p>

    {{-- Content --}}
    <div class="relative z-10 text-center max-w-md">
        <div class="h-14 w-14 rounded-2xl bg-amber-100 flex items-center justify-center mx-auto mb-5">
            <svg class="h-7 w-7 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874
                         1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Access denied</h1>
        <p class="text-gray-500 text-sm mb-8">
            You don't have permission to view this page. If you think this is an error, please contact a librarian.
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
