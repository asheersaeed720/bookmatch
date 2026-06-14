<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Rating;
use App\Policies\BookPolicy;
use App\Policies\BorrowPolicy;
use App\Policies\RatingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Rating::class, RatingPolicy::class);
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(Borrow::class, BorrowPolicy::class);
    }
}
