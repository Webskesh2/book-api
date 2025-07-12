<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Get a list of books with search capabilities.
     * Response: title, description, authors list, published date.
     * Search: title, description, author (can be by author_id).
     */
    public function index(Request $request)
    {
        $query = Book::with('authors');

        $this->applyFilters($query, $request);

        $books = $query->paginate(config('constants.pagination.authors_per_page'));

        return response()->json($books->map(function ($book) {
            return [
                'title' => $book->title,
                'description' => $book->description,
                'status' => $book->status,
                'categories' => $book->categories->pluck('name')->toArray(),
                'authors' => $book->authors->pluck('name')->toArray(),
                'published_date' => $book->published_date ? $book->published_date->format('Y-m-d') : null,
            ];
        }));
    }

    /**
     * @param $query
     * @param Request $request
     * @return void
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if ($request->has('description')) {
            $query->orWhere('description', 'like', '%' . $request->input('description') . '%');
        }

        if ($request->has('author')) {
            $authorName = $request->input('author');
            $query->whereHas('authors', function ($q) use ($authorName) {
                $q->where('name', 'like', '%' . $authorName . '%');
            });
        }

        if ($request->has('author_id')) {
            $authorId = $request->input('author_id');
            $query->whereHas('authors', function ($q) use ($authorId) {
                $q->where('id', $authorId);
            });
        }

        if ($request->has('status')) {
            $query->where('status', 'like', '%' . $request->input('status') . '%');
        }

        if ($request->has('category')) {
            $categoryName = $request->input('category');
            $query->whereHas('categories', function ($q) use ($categoryName) {
                $q->where('name', 'like', '%' . $categoryName . '%');
            });
        }

        if ($request->has('category_id')) {
            $categoryId = $request->input('category_id');
            $query->whereHas('authors', function ($q) use ($categoryId) {
                $q->where('id', $categoryId);
            });
        }
    }

    /**
     * Get books by a specific author.
     * Response: title, description, authors list, published date.
     */
    public function booksByAuthor(Author $author)
    {
        $books = $author->books()
            ->with('authors')
            ->paginate(config('constants.pagination.authors_per_page'));

        return response()->json($books->map(function ($book) {
            return [
                'title' => $book->title,
                'description' => $book->description,
                'authors' => $book->authors->pluck('name')->toArray(),
                'published_date' => $book->published_date ? $book->published_date->format('Y-m-d') : null,
            ];
        }));
    }
}
