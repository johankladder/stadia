<?php

namespace JohanKladder\Stadia\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends StadiaModel
{

    protected $table = "stadia_countries";

    protected $fillable = [
        'name',
        'code'
    ];

    public function calendarRanges()
    {
        return $this->hasMany(StadiaPlantCalendarRange::class);
    }

    public function durations()
    {
        return $this->hasMany(StadiaLevelDuration::class);
    }

    public function getRouteKeyName()
    {
        return 'code';
    }
}
