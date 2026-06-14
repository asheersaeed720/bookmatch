<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\BorrowDueSoon;
use App\Events\RatingApproved;
use App\Events\RatingSubmitted;
use App\Listeners\SendBorrowDueSoonNotification;
use App\Listeners\SendRatingApprovedNotification;
use App\Listeners\SendRatingSubmittedNotification;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Rating;
use App\Policies\BookPolicy;
use App\Policies\BorrowPolicy;
use App\Policies\RatingPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (Str::startsWith(config('app.url', ''), 'https') || app()->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::policy(Rating::class, RatingPolicy::class);
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(Borrow::class, BorrowPolicy::class);

        Event::listen(RatingSubmitted::class, SendRatingSubmittedNotification::class);
        Event::listen(RatingApproved::class, SendRatingApprovedNotification::class);
        Event::listen(BorrowDueSoon::class, SendBorrowDueSoonNotification::class);
    }
}
