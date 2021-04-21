<?php


namespace JohanKladder\Stadia;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;

class Stadia
{

    public function getAllPlants()
    {
        return Cache::remember('all-plants', 60 * 60, function () {
            return StadiaPlant::all();
        });
    }

    public function getAllCountries()
    {
        return Cache::remember('all-countries', 60 * 60, function () {
            return Country::all();
        });
    }

    public function getCalendarRanges(StadiaPlant $stadiaPlant, $country = null)
    {
        return Cache::remember('calendar-ranges-' . $stadiaPlant->id . ($country != null ? '-' . $country->id : ''), 60 * 60, function () use ($country, $stadiaPlant) {
            return $this->calendarRangesFactory($stadiaPlant, $country)->get();
        });
    }

    public function getCalendarRangesOfStadiaPlants(Collection $stadiaPlants, $country = null)
    {
        return $stadiaPlants->map(function ($stadiaPlant) use ($country) {
            return $this->getCalendarRanges($stadiaPlant, $country);
        });
    }

    public function getCalendarRangesOfAllStadiaPlants($country = null)
    {
        return $this->getCalendarRangesOfStadiaPlants(StadiaPlant::all(), $country);
    }

    private function calendarRangesFactory(StadiaPlant $stadiaPlant, $country)
    {
        $globalRanges = $stadiaPlant->calendarRanges()->whereNull('country_id');
        if ($country) {
            $countryRelated = $stadiaPlant->calendarRanges()->where('country_id', '=', $country->id);
            if ($countryRelated->count() > 0) {
                return $countryRelated;
            }
        }
        return $globalRanges;
    }

}
