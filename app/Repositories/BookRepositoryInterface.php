<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookRepositoryInterface
{
    public function getAllBooks(?string $search = null, int $page = 1): LengthAwarePaginator;
    public function getBooksByAuthorId(int $authorId): LengthAwarePaginator;
}
