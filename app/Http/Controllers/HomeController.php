<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Book::with('genre')
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('featured'));
    }
}
