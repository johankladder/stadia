<?php

namespace JohanKladder\Stadia\Models;

use JohanKladder\Stadia\Models\Information\StadiaHarvestInformation;
use JohanKladder\Stadia\Models\Interfaces\StadiaRelatedPlant;

class StadiaPlant extends StadiaModel
{

    protected $fillable = [
        'reference_id',
        'reference_table',
        'name',
    ];


    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getReferenceId()
    {
        return $this->reference_id;
    }

    public function getReferenceTable()
    {
        return $this->reference_table;
    }

    public function calendarRanges()
    {
        return $this->hasMany(StadiaPlantCalendarRange::class);
    }

    public function stadiaLevels()
    {
        return $this->hasMany(StadiaLevel::class);
    }

    public function durations()
    {
        return $this->hasManyThrough(StadiaLevelDuration::class, StadiaLevel::class);
    }

    public function getSupportedCountries()
    {
        return $this->calendarRanges()->whereNotNull('country_id')->get()->groupBy('country_id');
    }

    public function harvestInformation()
    {
        return $this->hasMany(StadiaHarvestInformation::class);
    }

    public function getReferencePlant(): ?StadiaRelatedPlant
    {
        $modelName = config("stadia.plant_model", null);
        if ($modelName) {
            return $modelName::find($this->reference_id);
        }
        return null;
    }


}
