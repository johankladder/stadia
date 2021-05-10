<?php

namespace JohanKladder\Stadia\Models;

use Illuminate\Database\Eloquent\Model;

class StadiaLevelDuration extends Model
{
    protected $fillable = [
        'duration',
        'stadia_level_id',
        'country_id',
        'climate_code_id'
    ];

    protected $with = [
        'country',
        'climateCode'
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

    public function stadiaLevel()
    {
        return $this->belongsTo(StadiaLevel::class);
    }
}
