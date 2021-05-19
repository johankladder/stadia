<?php


namespace JohanKladder\Stadia;


use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JohanKladder\Stadia\Exceptions\NoDurationsException;
use JohanKladder\Stadia\Exceptions\NoStadiaLevelsException;
use JohanKladder\Stadia\Exceptions\NoStadiaPlantFoundException;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\Interfaces\StadiaRelatedPlant;
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

    public function getCalendarRangesWithReference($referenceId, $country = null, $climateCode = null)
    {
        $stadiaPlant = StadiaPlant::where('reference_id', $referenceId)->first();
        if ($stadiaPlant) {
            return $this->getCalendarRanges($stadiaPlant, $country, $climateCode);
        }
        throw new NoStadiaPlantFoundException(
            "Can't find a StadiaPlant with the following reference_id: $referenceId"
        );
    }

    public function getCalendarRanges(StadiaPlant $stadiaPlant, $country = null, $climateCode = null)
    {
        return Cache::remember('calendar-ranges-' . $stadiaPlant->id . ($country != null ? '-' . $country->id : '') . ($climateCode != null ? '-' . $climateCode->id : ''), 60 * 60, function () use ($climateCode, $country, $stadiaPlant) {
            return $this->calendarRangesFactory($stadiaPlant, $country, $climateCode)->get();
        });
    }

    public function getCalendarRangesOf(Collection $stadiaPlants, $country = null, $climateCode = null)
    {
        return $stadiaPlants->map(function ($stadiaPlant) use ($climateCode, $country) {
            return [
                'reference_id' => $stadiaPlant->reference_id,
                'ranges' => $this->getCalendarRanges($stadiaPlant, $country, $climateCode)
            ];
        });
    }

    public function getCalendarRangesOfAllPlants($country = null, $climateCode = null)
    {
        return Cache::remember('calendar-ranges-all' . ($country != null ? '-' . $country->id : '') . ($climateCode != null ? '-' . $climateCode->id : ''), (60 * 60) * 24, function () use ($climateCode, $country) {
            return $this->getCalendarRangesOf(StadiaPlant::all(), $country, $climateCode);
        });
    }

    public function getGrowTime(StadiaPlant $stadiaPlant, $country = null, $climateCode = null)
    {
        $levels = $stadiaPlant->stadiaLevels;
        if ($levels->count() > 0) {
            $durations = 0;
            foreach ($levels as $level) {
                try {
                    $duration = $this->getDuration($level, $country, $climateCode);
                } catch (NoDurationsException $exception) {
                    $duration = 0;
                } finally {
                    $durations += $duration;
                }

            }
            return $durations;
        }
        throw new NoStadiaLevelsException(
            "This plant has no related levels yet."
        );
    }

    public function getCheckupDate(StadiaLevel $stadiaLevel, $country = null, $climateCode = null)
    {
        $duration = $this->getDuration($stadiaLevel, $country, $climateCode);
        return now()->addDays($duration)->roundDay();
    }

    public function getDuration(StadiaLevel $stadiaLevel, $country = null, $climateCode = null)
    {
        $duration = $this->durationsFactory($stadiaLevel, $country, $climateCode)->first();
        if ($duration != null) {
            return $duration->duration;
        }
        throw new NoDurationsException(
            "This level has no related durations yet."
        );
    }

    public function getDurations(StadiaPlant $stadiaPlant, $country = null, $climateCode = null)
    {
        $levels = $stadiaPlant->stadiaLevels;
        if ($levels->count() > 0) {
            $collection = Collection::make();
            foreach ($levels as $level) {
                $duration = $this->getDuration($level, $country, $climateCode);
                $level->duration = $duration;
                $collection->add($level);
            }
            return $collection;
        }
        throw new NoStadiaLevelsException(
            "This plant has no related levels yet."
        );
    }

    public function getSowable(Collection $referenceItems, CarbonInterface $currentDate, $country = null, $climateCode = null): Collection
    {
        return $referenceItems->filter(function (StadiaRelatedPlant $referenceItem) use ($climateCode, $country, $currentDate) {
            $stadiaPlant = StadiaPlant::where([
                'reference_id' => $referenceItem->getId(),
                'reference_table' => $referenceItem->getTableName()
            ])->first();

            if ($stadiaPlant != null) {
                $ranges = $this->getCalendarRanges($stadiaPlant, $country, $climateCode)
                    ->map(function ($item) use ($currentDate) {
                        $item->range_from = $item->range_from->setYear($currentDate->year);
                        $item->range_to = $item->range_to->setYear($currentDate->year);
                        return $item;
                    })
                    ->where('range_from', '<=', $currentDate->toDateTime())
                    ->where('range_to', '>=', $currentDate->toDateTime());

                if ($ranges->count() > 0) {
                    $referenceItem->sowable_till = $ranges->sortBy('range_to')
                        ->pluck('range_to')
                        ->filter(function (CarbonInterface $item) use ($currentDate) {
                            return $item->dayOfYear >= $currentDate->dayOfYear;
                        })->first();
                    return true;
                }
            }
            return false;
        });
    }

    private function calendarRangesFactory(StadiaPlant $stadiaPlant, $country = null, $climateCode = null)
    {
        $globalRanges = $stadiaPlant->calendarRanges()->whereNull('country_id');
        if ($country) {
            $countryRelated = $stadiaPlant->calendarRanges()->where('country_id', '=', $country->id);

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
