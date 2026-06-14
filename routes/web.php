<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Livewire\BookCatalogue;
use App\Livewire\BookDetail;
use App\Livewire\BookmarkPage;
use App\Livewire\HomePage;
use App\Livewire\UserDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');

Route::get('/books', BookCatalogue::class)->name('books.index');
Route::get('/books/{book:slug}', BookDetail::class)->name('books.show');

Route::middleware('auth')->group(function () {
    Route::post('/books/{book}/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');

    Route::get('/bookmarks', BookmarkPage::class)->name('bookmarks.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
});

require __DIR__.'/auth.php';
