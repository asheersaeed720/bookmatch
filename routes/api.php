<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\RecommendationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile App REST API (v1)
|--------------------------------------------------------------------------
|
| Token-authenticated (Laravel Sanctum) end-user API that mirrors the
| Breeze/Livewire user-facing features. Staff/admin actions remain in the
| Filament panel and are intentionally not exposed here.
|
*/

Route::prefix('v1')->group(function (): void {
    // --- Public ---------------------------------------------------------
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::get('genres', [GenreController::class, 'index']);
    Route::get('books', [BookController::class, 'index']);
    Route::get('books/{book:slug}', [BookController::class, 'show']);
    Route::get('books/{book:slug}/ratings', [RatingController::class, 'index']);

    // --- Authenticated (Sanctum) ---------------------------------------
    Route::middleware('auth:sanctum')->group(function (): void {
        // Auth / account
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/user', [AuthController::class, 'me']);
        Route::get('profile', [ProfileController::class, 'show']);
        Route::patch('profile', [ProfileController::class, 'update']);
        Route::put('profile/password', [ProfileController::class, 'updatePassword']);

        // Borrowing
        Route::get('borrows', [BorrowController::class, 'index']);
        Route::post('books/{book:slug}/borrow', [BorrowController::class, 'store']);

        // Bookmarks
        Route::get('bookmarks', [BookmarkController::class, 'index']);
        Route::post('books/{book:slug}/bookmark', [BookmarkController::class, 'toggle']);

        // Ratings (create/update own, delete own)
        Route::post('books/{book:slug}/ratings', [RatingController::class, 'store']);
        Route::delete('ratings/{rating}', [RatingController::class, 'destroy']);

        // Dashboard & recommendations
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('recommendations', [RecommendationController::class, 'index']);
    });
});
