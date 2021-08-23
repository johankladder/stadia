<?php


namespace JohanKladder\Stadia\Models\Information;


use Illuminate\Database\Eloquent\Model;
use JohanKladder\Stadia\Models\StadiaModel;

class KoepenLocation extends StadiaModel
{

    protected $fillable = [
        'latitude',
        'longitude',
        'code'
    ];

}
