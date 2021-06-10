<?php

namespace JohanKladder\Stadia\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Http\Requests\CalendarRangeRequest;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;

class CalendarController extends Controller
{
    public function index(Request $request, StadiaPlant $stadiaPlant)
    {
        $country = Country::find($request->query("country"));
        $climateCode = ClimateCode::find($request->query("climateCode"));

        $selectedCalendar = $country ? $country->calendarRanges()
            ->where('stadia_plant_id', $stadiaPlant->id)
            ->whereNull('climate_code_id')
            ->get() : Collection::make();

        $selectedCalendar = $climateCode && $country ? $climateCode->calendarRanges()
            ->where('stadia_plant_id', $stadiaPlant->id)
            ->where('country_id', $country->id)
            ->get()
            : $selectedCalendar;

        return view("stadia::stadia-plants-calendar.index", [
            'itemsGlobal' => $stadiaPlant->calendarRanges()->whereNull('country_id')->get(),
            'itemsCountry' => $stadiaPlant->calendarRanges()->whereNotNull('country_id')->whereNull('climate_code_id')->get()->groupBy(function ($item) {
                return $item->country->name;
            }),
            'itemsClimateCode' => $stadiaPlant->calendarRanges()->whereNotNull(['country_id', 'climate_code_id'])->get()->groupBy(function ($item) {
                return $item->country->name;
            }),
            'countries' => Country::all(),
            'climateCodes' => ClimateCode::all(),
            'plant' => $stadiaPlant,
            'selectedCalendar' => $selectedCalendar->count() > 0 ? $selectedCalendar : $stadiaPlant->calendarRanges()->whereNull('country_id')->get(),
            'selectedCountry' => $country,
            'selectedClimateCode' => $climateCode,
            'scatterInformation' => $this->getScatterInformation(
                $stadiaPlant,
                $country,
                $climateCode
            )
        ]);
    }

    private function getScatterInformation(StadiaPlant $stadiaPlant, ?Country $country, ?ClimateCode $climateCode)
    {
        $entries = Stadia::locationFactoryDefined($stadiaPlant->harvestInformation(), $country, $climateCode, true)->get();
        return $entries->map(function ($item) {
            return [
                'x' => $item->sow_date->dayOfYear,
                'y' => $item->harvest_date->dayOfYear
            ];
        });
    }

    public function storeCalendarRange(CalendarRangeRequest $request, StadiaPlant $stadiaPlant)
    {
        StadiaPlantCalendarRange::create(array_merge(
            $request->all(),
            ['stadia_plant_id' => $stadiaPlant->id]
        ));

        return redirect()->back();
    }

    public function destroy(StadiaPlantCalendarRange $calendarRange)
    {
        $calendarRange->delete();
        return redirect()->back();
    }
}
