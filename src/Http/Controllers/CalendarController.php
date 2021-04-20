<?php

namespace JohanKladder\Stadia\Http\Controllers;

use JohanKladder\Stadia\Http\Requests\CalendarRangeRequest;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;

class CalendarController extends Controller
{
    public function index(StadiaPlant $stadiaPlant)
    {
        return view("stadia::stadia-plants-calendar.index", [
            'itemsGlobal' => $stadiaPlant->calendarRanges()->whereNull('country_id')->get(),
            'itemsCountry' => $stadiaPlant->calendarRanges()->whereNotNull('country_id')->get()->groupBy(function ($item) {
                return $item->country->name;
            }),
            'countries' => Country::all(),
            'plant' => $stadiaPlant
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
