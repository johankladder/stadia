<?php


namespace JohanKladder\Stadia;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JohanKladder\Stadia\Exceptions\NoStadiaLevelsException;
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
        $levels = $stadiaPlant->stadiaLevels;
        if ($levels->count() > 0) {
            $durations = Collection::make();
            foreach ($levels as $level) {
                $duration = $this->durationsFactory($level, $country, $climateCode)->first();
                if ($duration != null) {
                    $durations->add($duration);
                }
            }
            return $durations->sum('duration');
        }
        throw new NoStadiaLevelsException(
            "This plant has no related levels yet."
        );
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

    private function durationsFactory(StadiaLevel $stadiaLevel, $country = null, $climateCode = null)
    {
        $globalDurations = $stadiaLevel->durations()->whereNull('country_id')->whereNull('climate_code_id');
        if ($country) {
            $countryRelated = $stadiaLevel->durations()->where('country_id', '=', $country->id);

            if ($climateCode) {
                $climateRelated = $countryRelated->where('climate_code_id', '=', $climateCode->id);
                if ($climateRelated->count() > 0) {
                    return $climateRelated;
                }
            }

            if ($countryRelated->count() > 0) {
                return $countryRelated;
            }
        }
        return $globalDurations;
    }

}
