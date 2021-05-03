<?php

namespace JohanKladder\Stadia\Logic;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JohanKladder\Stadia\Models\StadiaPlant;

class SyncLogic
{
    public function syncPlants($nameCallBack = null): Collection
    {
        $syncedEntities = Collection::make();
        $results = DB::select($this->queryFactory(
            config('stadia.plants_table_name'),
            config('stadia.plant_table_soft_deleted', false)
        ));
        foreach ($results as $plantEntity) {
            $entityId = $plantEntity->id;
            $entityName = $nameCallBack == null ? $plantEntity->name : call_user_func($nameCallBack, $plantEntity);
            $entity = StadiaPlant::firstOrCreate([
                'reference_id' => $entityId,
                'reference_table' => config('stadia.plants_table_name'),
                'name' => $entityName
            ]);
            $syncedEntities->add($entity);

        }
        return $syncedEntities;
    }

    private function queryFactory($table, $isSoftDeleted)
    {
        if ($isSoftDeleted) {
            return "select * from {$table} where deleted_at IS NULL";
        }
        return "select * from {$table}";
    }

}
