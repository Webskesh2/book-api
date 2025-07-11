<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Get a list of authors with the number of books.
     * Response: author name + number of books.
     * Search: author name.
     */
    public function index(Request $request)
    {
        $query = Author::withCount('books');

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        $authors = $query->paginate(config('constants.pagination.authors_per_page'));

        return response()->json($authors->map(function ($author) {
            return [
                'name' => $author->name,
                'book_count' => $author->books_count,
            ];
        }));
    }
}
