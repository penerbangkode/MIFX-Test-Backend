<?php

namespace App\Http\Controllers;

use App\Book;
use App\BookReview;
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BooksReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(["auth.admin", "auth:api"]);
    }

    public function store(int $bookId, PostBookReviewRequest $request)
    {
        $bookReview = Book::findOrFail($bookId)->reviews()->create([
            "review" => $request->get("review"),
            "comment" => $request->get("comment"),
            "user_id" => auth()->user()->id,
        ]);

        return response()->json([
            "data" => new BookReviewResource($bookReview),
        ], Response::HTTP_CREATED);
    }

    public function destroy(int $bookId, int $reviewId, Request $request)
    {
        $bookReview = Book::findOrFail($bookId)->reviews()->findOrFail($reviewId);
        $bookReview->delete();

        return response()->json([
            "data" => new BookReviewResource($bookReview),
        ], Response::HTTP_NO_CONTENT);
    }
}
