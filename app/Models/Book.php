<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Book
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $status
 * @property string $isbn
 * @property Carbon|null $published_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'isbn',
        'published_date',
    ];

    protected $casts = [
        'published_date' => 'date',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
