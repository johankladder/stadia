<?php

namespace JohanKladder\Stadia\Logic;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JohanKladder\Stadia\Models\StadiaPlant;

class SyncLogic
{
    public function syncPlants(string $tableName)
    {
        $syncedEntities = Collection::make();
        $results = DB::select("select * from {$tableName}");
        foreach ($results as $plantEntity) {
            $entityId = $plantEntity->id;
            $entity = StadiaPlant::firstOrCreate([
                'reference_id' => $entityId,
                'reference_table' => $tableName
            ]);
            $syncedEntities->add($entity);

        }
        return $syncedEntities;
    }

}
