<?php

namespace JohanKladder\Stadia\Models;

use Illuminate\Database\Eloquent\Model;

class StadiaPlantCalendarRange extends StadiaModel
{
    protected $fillable = [
        'range_from',
        'range_to',
        'stadia_plant_id',
        'country_id',
        'climate_code_id'
    ];

    protected $with = [
        'country',
        'climateCode'
    ];

    protected $casts = [
        'range_from' => 'date',
        'range_to' => 'date',
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

    public function climateCode()
    {
        return $this->belongsTo(ClimateCode::class);
    }
}
