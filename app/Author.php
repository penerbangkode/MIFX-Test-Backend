<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     protected $hidden = [
         "pivot"
     ];

    protected $fillable = [
        'name',
        'surname',
    ];
}
