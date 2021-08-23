<?php


namespace JohanKladder\Stadia\Models\Information;


use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaModel;
use JohanKladder\Stadia\Models\StadiaPlant;

class StadiaHarvestInformation extends StadiaModel
{

    protected $fillable = [
        'stadia_plant_id',
        'country_id',
        'climate_code_id',
        'sow_date',
        'harvest_date'
    ];

    protected $casts = [
        'sow_date' => 'date',
        'harvest_date' => 'date',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function climateCode()
    {
        return $this->belongsTo(ClimateCode::class);
    }

    public function stadiaPlant()
    {
        return $this->belongsTo(StadiaPlant::class);
    }

}
