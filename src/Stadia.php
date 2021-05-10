<?php


namespace JohanKladder\Stadia;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
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

    public function getCalendarRanges(StadiaPlant $stadiaPlant, $country = null, $climateCode = null)
    {
        return Cache::remember('calendar-ranges-' . $stadiaPlant->id . ($country != null ? '-' . $country->id : '') . ($climateCode != null ? '-' . $climateCode->id : ''), 60 * 60, function () use ($country, $stadiaPlant) {
            return $this->calendarRangesFactory($stadiaPlant, $country)->get();
        });
    }

    public function getCalendarRangesOf(Collection $stadiaPlants, $country = null, $climateCode = null)
    {
        return $stadiaPlants->map(function ($stadiaPlant) use ($climateCode, $country) {
            return $this->getCalendarRanges($stadiaPlant, $country, $climateCode);
        });
    }

    public function getCalendarRangesOfAllPlants($country = null)
    {
        return $this->getCalendarRangesOf(StadiaPlant::all(), $country);
    }

    public function getGrowTime(StadiaPlant $stadiaPlant, $country = null, $climateCode = null)
    {

    }

    public function getCheckupDate(StadiaPlant $stadiaPlant, StadiaLevel $stadiaLevel, $country = null, $climateCode = null)
    {

    }

    public function getDuration(StadiaLevel $stadiaLevel, $country = null, $climateCode = null)
    {

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
