<?php

namespace JohanKladder\Stadia\Models;

use Illuminate\Database\Eloquent\Model;

class StadiaPlantCalendarRange extends Model
{
    protected $fillable = [
        'range_from',
        'range_to',
        'stadia_plant_id',
        'country_id'
    ];

    protected $with = [
        'country'
    ];

    public function getDateFrom()
    {
        return $this->range_from;
    }

    public function getDateTo()
    {
        return $this->range_to;
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
