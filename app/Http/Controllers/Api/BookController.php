<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Repositories\BookRepositoryInterface;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected BookRepositoryInterface $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Get a list of books with search capabilities.
     * Response: title, description, authors list, published date.
     * Search: title, description, author (can be by author_id).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $page = $request->query('page', 1);

        return BookResource::collection($this->bookRepository->getAllBooks($search, $page));
    }

    /**
     * Get books by a specific author.
     * Response: title, description, authors list, published date.
     */
    public function booksByAuthor(int $authorId)
    {
        return BookResource::collection($this->bookRepository->getBooksByAuthorId($authorId));
    }
}
