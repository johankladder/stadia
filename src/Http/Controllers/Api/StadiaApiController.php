<?php


namespace JohanKladder\Stadia\Http\Controllers\Api;


use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Http\Controllers\Controller;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;

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

    public function calendarWithPlant(StadiaPlant $stadiaPlant, Country $country = null, ClimateCode $climateCode = null)
    {
        $stadiaPlant->ranges = Stadia::getCalendarRanges($stadiaPlant, $country, $climateCode);
        return [
            'data' => $stadiaPlant
        ];
    }

}
