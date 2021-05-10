<?php

namespace JohanKladder\Stadia\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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
        $selectedCalendar = $country ? $country->calendarRanges()->where('stadia_plant_id', $stadiaPlant->id)->get() : Collection::make();

        return view("stadia::stadia-plants-calendar.index", [
            'itemsGlobal' => $stadiaPlant->calendarRanges()->whereNull('country_id')->get(),
            'itemsCountry' => $stadiaPlant->calendarRanges()->whereNotNull('country_id')->get()->groupBy(function ($item) {
                return $item->country->name;
            }),
            'countries' => Country::all(),
            'climateCodes' => ClimateCode::all(),
            'plant' => $stadiaPlant,
            'selectedCalendar' => $selectedCalendar->count() > 0 ? $selectedCalendar : $stadiaPlant->calendarRanges()->whereNull('country_id')->get(),
            'selectedCountry' => $country
        ]);
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
