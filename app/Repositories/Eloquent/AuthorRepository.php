<?php

namespace App\Repositories\Eloquent;

use App\Models\Author;
use App\Repositories\AuthorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function getAllAuthors(?string $search = null): LengthAwarePaginator
    {
        $query = Author::withCount('books');
        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return $query->paginate(config('constants.pagination.max_per_page'));
    }
}
