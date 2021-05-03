<?php

namespace JohanKladder\Stadia\Models;

use Illuminate\Database\Eloquent\Model;

class StadiaLevel extends Model
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

}
