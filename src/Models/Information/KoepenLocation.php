<?php


namespace JohanKladder\Stadia\Models\Information;


use Illuminate\Database\Eloquent\Model;

class KoepenLocation extends Model
{

    protected $fillable = [
        'latitude',
        'longitude',
        'code'
    ];

}
