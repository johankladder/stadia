<?php

namespace JohanKladder\Stadia\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Http\Requests\DurationRequest;
use JohanKladder\Stadia\Logic\RegressionLogic;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaLevelDuration;
use JohanKladder\Stadia\Models\StadiaPlant;

class DurationController extends Controller
{
    public function index(Request $request, StadiaLevel $stadiaLevel)
    {
        $country = Country::find($request->query("country"));
        $climateCode = ClimateCode::find($request->query("climateCode"));

        $selectedDurations = $country ? $country->durations()
            ->where('stadia_level_id', $stadiaLevel->id)
            ->whereNull('climate_code_id')
            ->get() : Collection::make();

        $selectedDurations = $climateCode && $country ? $climateCode->durations()
            ->where('stadia_level_id', $stadiaLevel->id)
            ->where('country_id', $country->id)
            ->get()
            : $selectedDurations;


        return view("stadia::stadia-levels-duration.index", [
            'stadiaLevel' => $stadiaLevel,
            'countries' => Country::all(),
            'climateCodes' => ClimateCode::all(),
            'itemsGlobal' => $stadiaLevel->durations()->whereNull(['country_id', 'climate_code_id'])->get(),
            'itemsCountry' => $stadiaLevel->durations()->whereNotNull('country_id')->whereNull('climate_code_id')->get()->groupBy(function ($item) {
                return $item->country->name;
            }),
            'itemsClimateCode' => $stadiaLevel->durations()->whereNotNull(['country_id', 'climate_code_id'])->get()->groupBy(function ($item) {
                return $item->country->name;
            }),
            'selectedDurations' => $selectedDurations->count() > 0 ? $selectedDurations : $stadiaLevel->durations()->whereNull('country_id')->get(),
            'selectedCountry' => $country,
            'selectedClimateCode' => $climateCode,
            'scatterInformation' => $this->getScatterInformation(
                $stadiaLevel,
                $country,
                $climateCode
            ),
            'lineInformation' => $this->getRegressionLine(
                $stadiaLevel,
                $country,
                $climateCode
            )
        ]);
    }

    private function getScatterInformation(StadiaLevel $stadiaLevel, ?Country $country, ?ClimateCode $climateCode)
    {
        $entries = Stadia::locationFactoryDefined($stadiaLevel->levelInformation(), $country, $climateCode, true)->get();
        return $entries->map(function ($item) {
            $startDate = $item->start_date->dayOfYear;
            $duration = $item->end_date->diffInDays($item->start_date);
            return [
                'x' => $startDate,
                'y' => $startDate + $duration
            ];
        });
    }

    private function getRegressionLine(StadiaLevel $stadiaLevel, ?Country $country, ?ClimateCode $climateCode)
    {
        $entries = Stadia::locationFactoryDefined($stadiaLevel->levelInformation(), $country, $climateCode, true)->get();
        $logic = new RegressionLogic();
        $regression = $logic->createAndTrainLevelPrediction($entries);

        $slope = 0;
        $intercept = 0;
        if (count($regression->getCoefficients()) > 0) {
            $slope = $regression->getCoefficients()[0];
            $intercept = $regression->getIntercept();
        }

        return [
            'intercept' => $intercept,
            'slope' => $slope,
            'r2' => 'Unavailable',
            'line-values' => json_encode([
                [
                    'x' => 0,
                    'y' => $logic->getYCoordinateBestFit(0, $slope, $intercept)
                ],
                [
                    'x' => 365,
                    'y' => $logic->getYCoordinateBestFit(365, $slope, $intercept)
                ]
            ])
        ];
    }


    public function storeDuration(DurationRequest $request, StadiaLevel $stadiaLevel)
    {
        StadiaLevelDuration::firstOrCreate(array_merge(
            $request->validated(),
            ['stadia_level_id' => $stadiaLevel->id]
        ));

        return redirect()->back();
    }

    public function destroy(StadiaLevelDuration $stadiaDuration)
    {
        $stadiaDuration->delete();
        return redirect()->back();
    }
}
