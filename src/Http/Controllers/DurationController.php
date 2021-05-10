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

        return view("stadia::stadia-levels-duration.index", [
            'stadiaLevel' => $stadiaLevel,
            'countries' => Country::all(),
            'climateCodes' => ClimateCode::all(),
            'itemsGlobal' => Collection::make(),
            'itemsCountry' => Collection::make(),
        ]);
    }

    public function storeDuration(DurationRequest $request, StadiaLevel $stadiaLevel)
    {
        StadiaLevelDuration::create(array_merge(
            $request->all(),
            ['stadia_level_id' => $stadiaLevel->id]
        ));

        return redirect()->back();
    }

    public function destroy()
    {
        return redirect()->back();
    }
}
