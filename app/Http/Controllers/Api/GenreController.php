<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GenreController extends Controller
{
    /**
     * All genres, alphabetically, with their book counts.
     */
    public function index(): AnonymousResourceCollection
    {
        $genres = Genre::withCount('books')->orderBy('name')->get();

        return GenreResource::collection($genres);
    }
}
