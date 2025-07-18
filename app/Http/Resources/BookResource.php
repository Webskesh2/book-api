<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->pluck('name');
            }),
            'published_date' => $this->published_date ? $this->published_date->format('Y-m-d') : null,
            'status' => $this->status,
        ];
    }
}
