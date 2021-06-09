<?php


namespace JohanKladder\Stadia;


use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JohanKladder\Stadia\Exceptions\ClimateCodeNotFoundException;
use JohanKladder\Stadia\Exceptions\CountryNotFoundException;
use JohanKladder\Stadia\Exceptions\NoDurationsException;
use JohanKladder\Stadia\Exceptions\NoStadiaLevelFoundException;
use JohanKladder\Stadia\Exceptions\NoStadiaLevelsException;
use JohanKladder\Stadia\Exceptions\NoStadiaPlantFoundException;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\Information\KoepenLocation;
use JohanKladder\Stadia\Models\Information\StadiaHarvestInformation;
use JohanKladder\Stadia\Models\Information\StadiaLevelInformation;
use JohanKladder\Stadia\Models\Interfaces\StadiaRelatedPlant;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\Wrappers\LocationWrapper;

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

    public function getCalendarRangesWithReference($referenceId, ?LocationWrapper $locationWrapper = null)
    {
        $stadiaPlant = StadiaPlant::where('reference_id', $referenceId)->first();
        if ($stadiaPlant) {
            return $this->getCalendarRanges($stadiaPlant, $locationWrapper);
        }
        throw new NoStadiaPlantFoundException(
            "Can't find a StadiaPlant with the following reference_id: $referenceId"
        );
    }

    public function getCalendarRanges(StadiaPlant $stadiaPlant, ?LocationWrapper $locationWrapper = null)
    {
        [$country, $climateCode] = $this->getLocationInformation($locationWrapper);
        return Cache::remember('calendar-ranges-' . $stadiaPlant->id . ($country != null ? '-' . $country->id : '') . ($climateCode != null ? '-' . $climateCode->id : ''), 60 * 60, function () use ($locationWrapper, $stadiaPlant) {
            return $this->locationFactory($stadiaPlant->calendarRanges(), $locationWrapper)->get();
        });
    }

    public function getCalendarRangesOf(Collection $stadiaPlants, ?LocationWrapper $locationWrapper = null)
    {
        return $stadiaPlants->map(function ($stadiaPlant) use ($locationWrapper) {
            return [
                'reference_id' => $stadiaPlant->reference_id,
                'ranges' => $this->getCalendarRanges($stadiaPlant, $locationWrapper)
            ];
        });
    }

    public function getCalendarRangesOfAllPlants(?LocationWrapper $locationWrapper = null)
    {
        [$country, $climateCode] = $this->getLocationInformation($locationWrapper);
        return Cache::remember('calendar-ranges-all' . ($country != null ? '-' . $country->id : '') . ($climateCode != null ? '-' . $climateCode->id : ''), (60 * 60) * 24, function () use ($locationWrapper) {
            return $this->getCalendarRangesOf(StadiaPlant::all(), $locationWrapper);
        });
    }

    public function getGrowTime(StadiaPlant $stadiaPlant, ?LocationWrapper $locationWrapper = null)
    {
        $levels = $stadiaPlant->stadiaLevels;
        if ($levels->count() > 0) {
            $durations = 0;
            foreach ($levels as $level) {
                try {
                    $duration = $this->getDuration($level, $locationWrapper);
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

    public function getCheckupDate(StadiaLevel $stadiaLevel, ?LocationWrapper $locationWrapper = null)
    {
        $duration = $this->getDuration($stadiaLevel, $locationWrapper);
        return now()->addDays($duration)->roundDay();
    }

    public function getDuration(StadiaLevel $stadiaLevel, ?LocationWrapper $locationWrapper = null)
    {
        $duration = $this->locationFactory($stadiaLevel->durations(), $locationWrapper)->first();
        if ($duration != null) {
            return $duration->duration;
        }
        throw new NoDurationsException(
            "This level has no related durations yet."
        );
    }

    public function getDurations(StadiaPlant $stadiaPlant, ?LocationWrapper $locationWrapper = null)
    {
        $levels = $stadiaPlant->stadiaLevels;
        if ($levels->count() > 0) {
            $collection = Collection::make();
            foreach ($levels as $level) {
                $duration = $this->getDuration($level, $locationWrapper);
                $level->duration = $duration;
                $collection->add($level);
            }
            return $collection;
        }
        throw new NoStadiaLevelsException(
            "This plant has no related levels yet."
        );
    }

    public function getSowable(Collection $referenceItems, CarbonInterface $currentDate, ?LocationWrapper $locationWrapper = null): Collection
    {
        return $referenceItems->filter(function (StadiaRelatedPlant $referenceItem) use ($locationWrapper, $currentDate) {
            $stadiaPlant = $this->getStadiaPlantWithReference($referenceItem);
            if ($stadiaPlant != null) {
                $ranges = $this->getCalendarRanges($stadiaPlant, $locationWrapper)
                    ->map(fn($item) => $this->mapRangesToCurrentYear($item, $currentDate))
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

    public function getSowableFromDate(Collection $referenceItems, CarbonInterface $currentDate, ?LocationWrapper $locationWrapper = null)
    {
        return $referenceItems->filter(function (StadiaRelatedPlant $referenceItem) use ($locationWrapper, $currentDate) {
            $stadiaPlant = $this->getStadiaPlantWithReference($referenceItem);
            if ($stadiaPlant != null) {
                $ranges = $this->getCalendarRanges($stadiaPlant, $locationWrapper)
                    ->map(fn($item) => $this->mapRangesToCurrentYear($item, $currentDate));

                if ($ranges->count() > 0) {
                    $rangesLater = $ranges
                        ->sortBy('range_from')
                        ->pluck('range_from')
                        ->filter(function (CarbonInterface $item) use ($currentDate) {
                            return $item->dayOfYear >= $currentDate->dayOfYear;
                        })
                        ->first();

                    $rangesBefore = $ranges
                        ->sortBy('range_from')
                        ->pluck('range_from')
                        ->filter(function (CarbonInterface $item) use ($currentDate) {
                            return $item->dayOfYear <= $currentDate->dayOfYear;
                        })
                        ->first();

                    $referenceItem->sowable_from = $rangesLater ? $rangesLater : $rangesBefore;

                    return true;
                }
            }
            return false;
        });
    }


    public function storeHarvestInformation($referenceId, $sowDate, $harvestDate, ?LocationWrapper $locationWrapper = null)
    {
        $stadiaPlant = StadiaPlant::where('reference_id', $referenceId)->first();
        [$country, $climateCode] = $this->getLocationInformation($locationWrapper);
        if ($stadiaPlant != null) {
            StadiaHarvestInformation::create([
                'stadia_plant_id' => $stadiaPlant->id,
                'country_id' => $country != null ? $country->id : null,
                'climate_code_id' => $climateCode != null ? $climateCode->id : null,
                'sow_date' => $sowDate,
                'harvest_date' => $harvestDate
            ]);
        } else {
            throw new NoStadiaPlantFoundException(
                "Can't find a StadiaPlant with the following reference_id: $referenceId"
            );
        }
    }

    public function storeLevelInformation($referenceId, $startDate, $endDate, ?LocationWrapper $locationWrapper = null)
    {
        $stadiaLevel = StadiaLevel::where('reference_id', $referenceId)->first();
        [$country, $climateCode] = $this->getLocationInformation($locationWrapper);
        if ($stadiaLevel != null) {
            StadiaLevelInformation::create([
                'stadia_level_id' => $stadiaLevel->id,
                'country_id' => $country != null ? $country->id : null,
                'climate_code_id' => $climateCode != null ? $climateCode->id : null,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);
        } else {
            throw new NoStadiaLevelFoundException(
                "Can't find a StadiaLevel with the following reference_id: $referenceId"
            );
        }
    }

    public function getHarvestInformation(StadiaPlant $stadiaPlant, ?LocationWrapper $locationWrapper = null): Collection
    {
        return $this->locationFactory($stadiaPlant->harvestInformation(), $locationWrapper)->get();
    }

    public function getLevelInformation(StadiaLevel $stadiaLevel, ?LocationWrapper $locationWrapper = null): Collection
    {
        return $this->locationFactory($stadiaLevel->levelInformation(), $locationWrapper)->get();
    }

    public function getClimateCode($latitude, $longitude): ClimateCode
    {
        $lat = $this->roundCoordinates($latitude);
        $lon = $this->roundCoordinates($longitude);

        $koepenLocation = KoepenLocation::where('latitude', $lat)
            ->where('longitude', $lon)->first();

        if ($koepenLocation) {
            $climateCode = ClimateCode::where('code', $koepenLocation->code)->first();
            if ($climateCode) {
                return $climateCode;
            }
        }

        throw new ClimateCodeNotFoundException(
            "Could'nt find a climate code for given location"
        );
    }

    public function getCountry($countryCode): Country
    {
        $country = Country::where('code', $countryCode)->first();

        if ($country) {
            return $country;
        }

        throw new CountryNotFoundException(
            "Could'nt find a country for given location"
        );
    }

    private function roundCoordinates($rawCoord)
    {
        $decimal = fmod($rawCoord, 1);

        if ($decimal >= 0 && $decimal <= 0.5) {
            $coord = $rawCoord + (0.25 - $decimal);
        }

        if ($decimal > 0.5 && $decimal < 1) {
            $coord = $rawCoord + (0.75 - $decimal);
        }

        if ($decimal >= -0.5 && $decimal < 0) {
            $coord = $rawCoord + (-0.25 - $decimal);
        }

        if ($decimal > -1.00 && $decimal < -0.5) {
            $coord = $rawCoord + (-0.75 - $decimal);
        }

        return $coord;
    }

    private function mapRangesToCurrentYear($item, $currentDate)
    {
        $item->range_from = $item->range_from->setYear($currentDate->year);
        $item->range_to = $item->range_to->setYear($currentDate->year);
        return $item;
    }

    private function getStadiaPlantWithReference(StadiaRelatedPlant $relatedPlant)
    {
        return StadiaPlant::where([
            'reference_id' => $relatedPlant->getId(),
            'reference_table' => $relatedPlant->getTableName()
        ])->first();
    }

    private function locationFactory(HasMany $builder, ?LocationWrapper $locationWrapper = null): HasMany
    {

        [$country, $climateCode] = $this->getLocationInformation($locationWrapper);

        $newBuilder = clone $builder;

        if ($country != null) {
            $newBuilder = $newBuilder->where('country_id', $country->id);
        }

        if ($climateCode != null) {
            $newBuilder = $newBuilder->where('climate_code_id', $climateCode->id);
        }

        if (($climateCode == null && $country == null) || $newBuilder->count() <= 0) {
            $newBuilder = $builder->whereNull('country_id')
                ->whereNull('climate_code_id');
        }

        return $newBuilder;
    }

    private function getLocationInformation(?LocationWrapper $locationWrapper = null)
    {
        $countryCode = $locationWrapper ? $locationWrapper->countryCode : null;
        $latitude = $locationWrapper ? $locationWrapper->latitude : null;
        $longitude = $locationWrapper ? $locationWrapper->longitude : null;

        try {
            $country = $this->getCountry($countryCode);

        } catch (CountryNotFoundException $exception) {
            $country = null;
        }

        try {
            $climateCode = $this->getClimateCode($latitude, $longitude);
        } catch (ClimateCodeNotFoundException $exception) {
            $climateCode = null;
        }

        return [
            $country, $climateCode
        ];
    }

}
