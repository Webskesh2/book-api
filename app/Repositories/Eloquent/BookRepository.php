<?php

namespace App\Repositories\Eloquent;

use App\Models\Author;
use App\Models\Book;
use App\Repositories\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class BookRepository implements BookRepositoryInterface
{
    public function getAllBooks(?string $search = null, int $page = 1): LengthAwarePaginator
    {
        $query = Book::with('authors', 'categories');

        if ($search) {
            $query->where('title', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->whereHas('authors', function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%');
                })
                ->whereHas('authors', function ($q) use ($search) {
                    $q->where('id', $search);
                });
        }

        return $query->paginate(config('constants.pagination.max_per_page'), ['*'], 'page', $page);
    }

    public function getBooksByAuthorId(int $authorId): LengthAwarePaginator
    {
        $author = Author::findOrFail($authorId);

        return $author->books()->with('authors', 'categories')->paginate(config('constants.pagination.max_per_page'));
    }
}
