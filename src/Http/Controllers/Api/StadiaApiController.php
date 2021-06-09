<?php


namespace JohanKladder\Stadia\Http\Controllers\Api;


use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Http\Controllers\Controller;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\Wrappers\LocationWrapper;

class StadiaApiController extends Controller
{

    public function calendar(Country $country = null, $latitude = null, $longitude = null)
    {
        return [
            'data' => Stadia::getAllPlants()->map(function ($item) use ($longitude, $latitude, $country) {
                $item->ranges = Stadia::getCalendarRanges($item, new LocationWrapper(
                    $country ? $country->code : null,
                    $latitude,
                    $longitude
                ));
                return $item;
            })
        ];
    }

    public function calendarWithPlant(StadiaPlant $stadiaPlant, Country $country = null, $latitude = null, $longitude = null)
    {
        $stadiaPlant->ranges = Stadia::getCalendarRanges($stadiaPlant,
            new LocationWrapper(
                $country ? $country->code : null,
                $latitude,
                $longitude
            ));
        return [
            'data' => $stadiaPlant
        ];
    }

}
