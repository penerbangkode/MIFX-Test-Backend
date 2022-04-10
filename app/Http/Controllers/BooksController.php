<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BooksController extends Controller
{
    public function __construct()
    {
        $this->middleware(["auth.admin", "auth:api"])->except(['index']);
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
            ]);

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
            15, // default 15 items per page
            ['*'], // Columns to select,
            'page', // Name of the page query param
            $request->input('page', 1) // Default page
        );

        return response()->json([
            "data" => BookResource::collection($books),
            "links" => [
                "first" => $books->url(1),
                "last" => $books->url($books->lastPage()),
                "prev" => $books->previousPageUrl(),
                "next" => $books->nextPageUrl(),
            ],
            "meta" => [
                "current_page" => $books->currentPage(),
                "from" => $books->firstItem(),
                "last_page" => $books->lastPage(),
                "path" => $books->url(1),
                "per_page" => $books->perPage(),
                "to" => $books->lastItem(),
                "total" => $books->total(),
            ]
        ], Response::HTTP_OK);
    }

    public function store(PostBookRequest $request)
    {
        DB::beginTransaction();
        try{
            $book = new Book();
            $book->fill($request->all())->save();
            $book->authors()->sync($request->get("authors"));
            DB::commit();
            return response()->json([
                "data" => new BookResource($book->load("authors"))
            ], Response::HTTP_CREATED);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json([
                "error" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
