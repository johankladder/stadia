<?php


namespace JohanKladder\Stadia\Http\Controllers\Api;


use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Http\Controllers\Controller;
use JohanKladder\Stadia\Http\Resources\CalendarResource;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;

class StadiaApiController extends Controller
{

    public function calendar(Country $country = null, ClimateCode $climateCode = null)
    {
        return [
            'data' => Stadia::getAllPlants()->map(function ($item) use ($climateCode, $country) {
                $item->ranges = Stadia::getCalendarRanges($item, $country, $climateCode);
                return $item;
            })
        ];
    }

}
