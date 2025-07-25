<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name',
        'category_id',
        'slug',
        'price',
        'image',
        'description'
    ];
}
