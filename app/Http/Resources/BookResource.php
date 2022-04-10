<?php

namespace App\Http\Resources;

use App\Book;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "isbn" => $this->isbn,
            "title" => $this->title,
            "description" => $this->description,
            "published_year" => $this->published_year,
            "authors" => AuthorResource::collection($this->authors),
            "review" => [
                "avg" => (int) round($this->avg_review),
                "count" =>(int) round($this->reviews_count),
            ],
        ];
    }
}
