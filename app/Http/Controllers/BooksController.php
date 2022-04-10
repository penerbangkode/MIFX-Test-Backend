<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BooksController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "sortColumn" => Rule::in(["title", "avg_review", "published_year"]),
            "sortDirection" => Rule::in(["ASC", "DESC"]),

        ]);
        if ($validator->fails()) {
            return response()->json([
                "error" => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $books = Book::withCount([
                'reviews',
                // Average Review 
                'reviews as avg_review' => function ($query) {
                    $query->select(\DB::raw('coalesce(avg(review), 0)'));
                }
            ])->with("authors");

        if ($request->has("sortColumn")) {
            $books->orderBy($request->get("sortColumn"), $request->get("sortDirection", "asc"));
        }

        if ($request->has("authors")){
            $books->whereHas("authors", function ($query) use ($request) {
                $query->whereIn("id", explode(",", $request->get("authors")));
            });
        }

        if($request->has("title")){
            $books->where("title", "like", "%{$request->get("title")}%");
        }

        $books = $books->paginate(
            15, // default 10 items per page
            ['*'], // Columns to select,
            'page', // Name of the page query param
            $request->input('page', 1) // Default page
        );

        return response()->json(new BookResource($books), Response::HTTP_OK);
    }

    public function store(PostBookRequest $request)
    {
        // @TODO implement
        $book = new Book();

        return new BookResource($book);
    }
}
