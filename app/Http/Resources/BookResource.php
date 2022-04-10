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
            "data" => array_map(function ($book) {
                $review = [
                    "count" => (int) round($book["reviews_count"]),
                    "avg" => (int) round($book["avg_review"])
                ];
                unset($book["avg_review"]);
                unset($book["reviews_count"]);
                return  array_merge($book->toArray(), ["review" => $review]);;
            }, $this->items()),
            "links" => [
                "first" => $this->url(1),
                "last" => $this->url($this->lastPage()),
                "prev" => $this->previousPageUrl(),
                "next" => $this->nextPageUrl()
            ],
            "meta" => [
                "current_page" => $this->currentPage(),
                "from" => $this->firstItem(),
                "last_page" => $this->lastPage(),
                "path" => $this->path(),
                "per_page" => $this->perPage(),
                "to" => $this->lastItem(),
                "total" => $this->total()
            ]
        ];
    }
}
