<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\AuthorRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorResource;

class AuthorController extends Controller
{
    protected AuthorRepositoryInterface $authorRepository;

    public function __construct(AuthorRepositoryInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * Get a list of authors with the number of books.
     * Response: author name + number of books.
     * Search: author name.
     */
    public function index(Request $request)
    {
        return AuthorResource::collection($this->authorRepository->getAllAuthors($request->query('search')));
    }
}
