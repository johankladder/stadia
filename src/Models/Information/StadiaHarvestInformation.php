<?php


namespace JohanKladder\Stadia\Models\Information;


use Illuminate\Database\Eloquent\Model;

class StadiaHarvestInformation extends Model
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

}
