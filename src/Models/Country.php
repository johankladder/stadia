<?php

namespace JohanKladder\Stadia\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{

    protected $fillable = [
        'name',
        'code'
    ];

}
