<?php

namespace JohanKladder\Stadia\Http\Controllers;


use Illuminate\Database\QueryException;
use JohanKladder\Stadia\Logic\SyncLogic;
use JohanKladder\Stadia\Models\StadiaPlant;

class StadiaPlantController extends Controller
{

    public function index()
    {
        return view("stadia::stadia-plants.index", [
            'items' => StadiaPlant::all()
        ]);
    }

    public function destroy(StadiaPlant $stadiaPlant)
    {
        $stadiaPlant->delete();
        return redirect()->route('stadia-plants.index')->with(['message' => 'Plant deleted!', 'alert' => 'alert-success']);
    }

    public function sync()
    {
        try {
            $syncedEntities = (new SyncLogic())->syncPlants();
            return redirect()->route('stadia-plants.index')->with(['message' => "Sync completed - added {$syncedEntities->count()} items!", 'alert' => 'alert-success']);
        } catch (QueryException $exception) {
            return redirect()->route('stadia-plants.index')->with(['message' => "Could'nt sync items! {$exception->getMessage()}", 'alert' => 'alert-danger']);
        }
    }
}
