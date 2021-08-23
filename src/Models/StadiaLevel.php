<?php

namespace JohanKladder\Stadia\Models;

use Illuminate\Database\Eloquent\Model;
use JohanKladder\Stadia\Models\Information\StadiaLevelInformation;
use JohanKladder\Stadia\Models\Interfaces\StadiaRelatedLevel;

class StadiaLevel extends StadiaModel
{

    protected $fillable = [
        'reference_id',
        'reference_table',
        'name',
        'stadia_plant_id'
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

    public function stadiaPlant()
    {
        return $this->belongsTo(StadiaPlant::class);
    }

    public function durations()
    {
        return $this->hasMany(StadiaLevelDuration::class);
    }

    public function getSupportedCountries()
    {
        return $this->durations()->whereNotNull('country_id')->get()->groupBy('country_id');
    }

    public function levelInformation()
    {
        return $this->hasMany(StadiaLevelInformation::class);
    }

    public function getReferenceLevel(): ?StadiaRelatedLevel
    {
        $modelName = config("stadia.level_model", null);
        if ($modelName) {
            return $modelName::find($this->reference_id);
        }
        return null;
    }

}
