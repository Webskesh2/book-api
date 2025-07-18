<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Repositories\BookRepositoryInterface;
use App\Services\BookServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected BookRepositoryInterface $bookRepository;
    protected BookServiceInterface $bookService;

    public function __construct(BookRepositoryInterface $bookRepository, BookServiceInterface $bookService)
    {
        $this->bookRepository = $bookRepository;
        $this->bookService = $bookService;
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

    /**
     * No API for this method yet.
     *
     * @return JsonResponse
     */
    public function publish(Book $book)
    {
        $this->bookService->publishBook($book);

        return response()->json(['message' => 'Book published successfully.']);
    }
}
