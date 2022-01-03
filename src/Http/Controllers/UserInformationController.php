<?php

namespace JohanKladder\Stadia\Http\Controllers;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JohanKladder\Stadia\Models\Information\StadiaHarvestInformation;
use JohanKladder\Stadia\Models\Information\StadiaLevelInformation;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaPlant;

class UserInformationController extends Controller
{

    public function index()
    {
        return view("stadia::user-information.index", [
            'mostHarvest' => $this->getMostHarvest(),
            'mostActivity' => $this->getMostActivity()
        ]);
    }

    private function getMostHarvest(): Collection
    {
        $currentMonth = now()->month;
        $previousMonth = ($currentMonth - 1 > 0) ? $currentMonth - 1 : 1;
        $items = StadiaHarvestInformation::orderBy('count', 'desc')
            ->whereMonth('created_at', '>=', $previousMonth)
            ->whereMonth('created_at', '<=', $currentMonth)
            ->whereYear('created_at', now()->year)
            ->select(DB::raw('stadia_plant_id,count(*) as count'))
            ->groupBy('stadia_plant_id')
            ->limit(5)
            ->get();

        return $items->map(function ($item) {
            $stadiaPlant = StadiaPlant::find($item["stadia_plant_id"]);
            $stadiaPlant->harvest_count = $item['count'];
            return $stadiaPlant;
        });
    }

    private function getMostActivity(): Collection
    {
        $currentMonth = now()->month;
        $previousMonth = ($currentMonth - 1 > 0) ? $currentMonth - 1 : 12;
        $items = StadiaLevelInformation::orderBy('count', 'desc')
            ->whereMonth('created_at', '>=', $previousMonth)
            ->whereMonth('created_at', '<=', $currentMonth)
            ->whereYear('created_at', now()->year)
            ->select(DB::raw('stadia_level_id,count(*) as count'))
            ->groupBy('stadia_level_id')
            ->limit(5)
            ->get();

        return $items->map(function ($item) {
            $stadiaLevel = StadiaLevel::find($item["stadia_level_id"]);
            $stadiaLevel->activity_count = $item['count'];
            return $stadiaLevel;
        });
    }
}


