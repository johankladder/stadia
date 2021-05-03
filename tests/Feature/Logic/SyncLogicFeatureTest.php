<?php


namespace JohanKladder\Stadia\Tests\Feature\Logic;


use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JohanKladder\Stadia\Logic\SyncLogic;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;

class SyncLogicFeatureTest extends TestCase
{
    protected $syncLogic;

    public function setUp(): void
    {
        parent::setUp();
        $this->syncLogic = new SyncLogic();
    }

    /** @test */
    public function sync_non_existing_database()
    {
        $this->expectException(QueryException::class);
        $this->syncLogic->syncPlants('non-existing-table-name');
    }

    /** @test */
    public function sync_non_existing_database_levels()
    {
        $this->expectException(QueryException::class);
        $this->syncLogic->syncLevels();
    }

    /** @test */
    public function sync_existing_empty_database()
    {
        $this->createPlantRelatedTable('plants');
        $collection = $this->syncLogic->syncPlants('plants');
        $this->assertCount(0, $collection);
    }

    /** @test */
    public function sync_existing_empty_database_levels()
    {
        $this->createLevelRelatedTable('levels');
        $collection = $this->syncLogic->syncLevels();
        $this->assertCount(0, $collection);
    }

    /** @test */
    public function sync_existing_database()
    {
        $this->createPlantRelatedTable('plants');
        $this->insertPlantInPlantRelatedTable('plants');
        $collection = $this->syncLogic->syncPlants();
        $this->assertCount(5, $collection);

        $collection->each(function ($item, $index) {
            $this->assertEquals("Name", $item->name);
            $this->assertEquals($index, $item->reference_id);
            $this->assertEquals("plants", $item->reference_table);
        });
    }

    /** @test */
    public function sync_existing_database_levels()
    {
        $referencePlant = StadiaPlant::create([
            'reference_id' => 0
        ]);
        $this->createLevelRelatedTable('levels');
        $this->insertLevelInLevelRelatedTable('levels');
        $collection = $this->syncLogic->syncLevels();
        $this->assertCount(5, $collection);

        $collection->each(function ($item, $index) use ($referencePlant) {
            $this->assertEquals("Name", $item->name);
            $this->assertEquals($index, $item->reference_id);
            $this->assertEquals("levels", $item->reference_table);
            $this->assertEquals($referencePlant->id, $item->stadia_plant_id);
        });
    }

    /** @test */
    public function sync_existing_database_levels_unknown_relation()
    {
        StadiaPlant::create([
            'reference_id' => 1
        ]);
        $this->createLevelRelatedTable('levels');
        $this->insertLevelInLevelRelatedTable('levels');
        $collection = $this->syncLogic->syncLevels();
        $this->assertCount(0, $collection);
    }

    /** @test */
    public function sync_existing_database_with_specific_name_identifier()
    {
        $this->createPlantRelatedTable('plants');
        $this->insertPlantInPlantRelatedTable('plants', 5, json_encode([
            "en" => "English name",
            "nl" => "Dutch name"
        ]));
        $collection = $this->syncLogic->syncPlants(function ($entity) {
            return json_decode($entity->name)->en;
        });
        $this->assertCount(5, $collection);

        $collection->each(function ($item, $index) {
            $this->assertEquals("English name", $item->name);
            $this->assertEquals($index, $item->reference_id);
            $this->assertEquals("plants", $item->reference_table);
        });
    }

    /** @test */
    public function sync_existing_database_when_provided_as_soft_deleted_table()
    {
        Config::set("stadia.plant_table_soft_deleted", true);
        $this->createPlantRelatedTable('plants', true);
        $this->insertPlantInPlantRelatedTable("plants", 5, "name", 0, true, null);
        $this->insertPlantInPlantRelatedTable("plants", 5, "soft_deleted", 5, true, now());
        $collection = $this->syncLogic->syncPlants();
        $this->assertCount(5, $collection);
        $collection->each(function ($item, $index) {
            $this->assertEquals("name", $item->name);
            $this->assertEquals($index, $item->reference_id);
            $this->assertEquals("plants", $item->reference_table);
        });
    }

    private function createPlantRelatedTable($tableName, $softDeleted = false)
    {
        Schema::create($tableName, function ($table) use ($softDeleted) {
            $table->temporary();
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
            if ($softDeleted) {
                $table->softDeletes();
            }
        });
    }

    private function createLevelRelatedTable($tableName, $softDeleted = false)
    {
        Schema::create($tableName, function ($table) use ($softDeleted) {
            $table->temporary();
            $table->increments('id');
            $table->string('name');
            $table->integer("plant_id");
            $table->timestamps();
            if ($softDeleted) {
                $table->softDeletes();
            }
        });
    }

    public function insertPlantInPlantRelatedTable($tableName, $n = 5, $name = "Name", $fromId = 0, $softDeleted = false, $softDeletedValue = null)
    {
        for ($i = 0; $i < $n; $i++) {
            if ($softDeleted) {
                DB::insert("insert into " . $tableName . " (id, name, deleted_at) values (?, ?, ?)", [($i + $fromId), $name, $softDeletedValue]);
            } else {
                DB::insert("insert into " . $tableName . " (id, name) values (?, ?)", [($i + $fromId), $name]);
            }
        }
    }

    public function insertLevelInLevelRelatedTable($tableName, $n = 5, $name = "Name", $plantId = 0, $fromId = 0, $softDeleted = false, $softDeletedValue = null)
    {
        for ($i = 0; $i < $n; $i++) {
            if ($softDeleted) {
                DB::insert("insert into " . $tableName . " (id, name, plant_id, deleted_at) values (?, ?, ?, ?)", [($i + $fromId), $name, $plantId, $softDeletedValue]);
            } else {
                DB::insert("insert into " . $tableName . " (id, name, plant_id) values (?, ?, ?)", [($i + $fromId), $name, $plantId]);
            }
        }
    }

}
