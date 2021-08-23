<?php


namespace JohanKladder\Stadia\Models\Information;


use Illuminate\Database\Eloquent\Model;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaModel;

class StadiaLevelInformation extends StadiaModel
{

    protected $fillable = [
        'stadia_level_id',
        'country_id',
        'climate_code_id',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

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
