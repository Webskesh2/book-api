<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
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
}
