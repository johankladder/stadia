<?php

namespace JohanKladder\Stadia\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use JohanKladder\Stadia\Http\Requests\DurationRequest;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaLevelDuration;

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
            'selectedClimateCode' => $climateCode
        ]);
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
