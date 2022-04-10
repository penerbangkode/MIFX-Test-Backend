<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class PostBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "isbn" => "required|size:13|unique:books,isbn",
            "title" => "required",
            "description" => "required",
            "authors" => "required|array",
            "authors.*" => "required|exists:authors,id",
            "published_year" => "required|integer|min:1900|max:2020",
        ];
    }
}
