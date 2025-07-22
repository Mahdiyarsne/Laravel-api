<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    //
    protected $fillable = [
        'user_id',
        'address_line_one',
        'address_line_two',
        'city',
        'state',
        'zip',
        'country'

    ];
}
