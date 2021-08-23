<?php


namespace JohanKladder\Stadia\Models;


use Illuminate\Database\Eloquent\Model;

class StadiaModel extends Model
{

    public function getConnectionName()
    {
        return config("stadia.connection_name", "");
    }

}
