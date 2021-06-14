<?php

namespace JohanKladder\Stadia\Logic;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JohanKladder\Stadia\Models\Interfaces\StadiaRelatedPlant;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaPlant;

class SyncLogic
{
    public function syncPlants($nameCallBack = null): Collection
    {
        $syncedEntities = Collection::make();

        $modelName = config("stadia.plant_model", null);
        if ($modelName) {
            $results = $modelName::all();
        } else {
            $results = DB::select($this->queryFactory(
                config('stadia.plants_table_name'),
                config('stadia.plant_table_soft_deleted', false)
            ));
        }

        foreach ($results as $plantEntity) {
            $entity = $this->syncPlantEntity($plantEntity, $nameCallBack, $modelName);
            $syncedEntities->add($entity);

        }
        return $syncedEntities;
    }

    private function syncPlantEntity($plantEntity, $nameCallBack, $modelName = null): StadiaPlant
    {
        if ($modelName) {
            if (new $modelName() instanceof StadiaRelatedPlant) {
                $entity = $this->getOrCreateStadiaPlant(
                    $plantEntity->getId(),
                    $plantEntity->getTableName()
                );
                $entity->name = $plantEntity->getFormattedName();
                $entity->save();
                return $entity->refresh();
            }
        }

        $entity = $this->getOrCreateStadiaPlant($plantEntity->id, config('stadia.plants_table_name'));
        $entityName = $nameCallBack == null ? $plantEntity->name : call_user_func($nameCallBack, $plantEntity);
        $entity->name = $entityName;
        $entity->save();

        return $entity->refresh();
    }

    private function getOrCreateStadiaPlant($referenceId, $tableName): StadiaPlant
    {
        return StadiaPlant::firstOrCreate([
            'reference_id' => $referenceId,
            'reference_table' => $tableName,
        ]);
    }

    public function syncLevels($nameCallBack = null): Collection
    {
        $syncedEntities = Collection::make();
        $results = DB::select($this->queryFactory(
            config('stadia.levels_table_name'),
            config('stadia.levels_table_soft_deleted', false)
        ));
        foreach ($results as $levelEntity) {
            $entityId = $levelEntity->id;
            $plantReferenceId = $levelEntity->plant_id;
            $entityName = $nameCallBack == null ? $levelEntity->name : call_user_func($nameCallBack, $levelEntity);
            $stadiaPlant = StadiaPlant::where('reference_id', $plantReferenceId)->first();
            if ($stadiaPlant) {
                $entity = StadiaLevel::firstOrCreate([
                    'reference_id' => $entityId,
                    'reference_table' => config('stadia.levels_table_name'),
                    'name' => $entityName,
                    'stadia_plant_id' => $stadiaPlant->id
                ]);
                $syncedEntities->add($entity);
            }
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
