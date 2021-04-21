<?php

namespace JohanKladder\Stadia\Facades;

use Illuminate\Support\Facades\Facade;

class Stadia extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'stadia';
    }

}
