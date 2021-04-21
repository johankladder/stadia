<?php

namespace JohanKladder\Stadia\Models;

use Illuminate\Database\Eloquent\Model;

class StadiaPlant extends Model
{

    protected $fillable = [
        'reference_id',
        'reference_table',
        'name'
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

}
