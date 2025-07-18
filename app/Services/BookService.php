<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\BookRepositoryInterface;
use InvalidArgumentException;

class BookService implements BookServiceInterface
{
    public function __construct(
        protected BookRepositoryInterface $bookRepository
    ) {}

    public function publishBook(Book $book): void
    {
        if ($book->status === 'published') {
            throw new InvalidArgumentException('Book is already published.');
        }

        $book->status = 'published';
        $book->published_date = now();
        $book->save();
    }
}
