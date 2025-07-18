<?php

namespace App\Services;

use App\Models\Book;

interface BookServiceInterface
{
    public function publishBook(Book $book): void;
}
