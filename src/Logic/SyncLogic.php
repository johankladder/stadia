<?php

namespace JohanKladder\Stadia\Logic;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JohanKladder\Stadia\Models\StadiaPlant;

class SyncLogic
{
    public function syncPlants(string $tableName, $nameCallBack = null): Collection
    {
        $syncedEntities = Collection::make();
        $results = DB::select("select * from {$tableName}");
        foreach ($results as $plantEntity) {
            $entityId = $plantEntity->id;
            $entityName = $nameCallBack == null ? $plantEntity->name : call_user_func($nameCallBack, $plantEntity);
            $entity = StadiaPlant::firstOrCreate([
                'reference_id' => $entityId,
                'reference_table' => $tableName,
                'name' => $entityName
            ]);
            $syncedEntities->add($entity);

        }
        return $syncedEntities;
    }

}
