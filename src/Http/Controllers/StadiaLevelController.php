<?php

namespace JohanKladder\Stadia\Http\Controllers;


use Illuminate\Database\QueryException;
use JohanKladder\Stadia\Logic\SyncLogic;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaPlant;

class StadiaLevelController extends Controller
{

    public function index(StadiaPlant $stadiaPlant)
    {
        return view("stadia::stadia-levels.index", [
            'items' => $stadiaPlant->stadiaLevels()->get(),
            'stadiaPlant' => $stadiaPlant
        ]);
    }

    public function destroy(StadiaLevel $stadiaLevel)
    {
        $stadiaLevel->delete();
        return redirect()->route('stadia-levels.index', $stadiaLevel->stadiaPlant)->with(['message' => 'Level deleted!', 'alert' => 'alert-success']);
    }

    public function show(StadiaLevel $stadiaLevel)
    {

    }

    public function sync(StadiaPlant $stadiaPlant)
    {
        try {
            $syncedEntities = (new SyncLogic())->syncLevels();
            return redirect()->route('stadia-levels.index', $stadiaPlant)->with(['message' => "Sync completed - added {$syncedEntities->count()} items!", 'alert' => 'alert-success']);
        } catch (QueryException $exception) {
            return redirect()->route('stadia-levels.index', $stadiaPlant)->with(['message' => "Could'nt sync items! {$exception->getMessage()}", 'alert' => 'alert-danger']);
        }
    }
}
