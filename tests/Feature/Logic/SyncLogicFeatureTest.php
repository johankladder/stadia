<?php


namespace JohanKladder\Stadia\Tests\Feature\Logic;


use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JohanKladder\Stadia\Logic\SyncLogic;
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
    public function sync_existing_empty_database()
    {
        $this->createPlantRelatedTable('plants');
        $collection = $this->syncLogic->syncPlants('plants');
        $this->assertCount(0, $collection);
    }

    /** @test */
    public function sync_existing_database()
    {
        $this->createPlantRelatedTable('plants');
        $this->insertPlantInPlantRelatedTable('plants');
        $collection = $this->syncLogic->syncPlants('plants');
        $this->assertCount(5, $collection);

        $collection->each(function ($item, $index) {
            $this->assertEquals("Name", $item->name);
            $this->assertEquals($index, $item->reference_id);
            $this->assertEquals("plants", $item->reference_table);
        });
    }

    /** @test */
    public function sync_existing_database_with_specific_name_identifier()
    {
        $this->createPlantRelatedTable('plants');
        $this->insertPlantInPlantRelatedTable('plants', 5, json_encode([
            "en" => "English name",
            "nl" => "Dutch name"
        ]));
        $collection = $this->syncLogic->syncPlants('plants', function ($entity) {
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
        Config::set("stadia.soft_deleted_tables", ['plants']);
        $this->createPlantRelatedTable('plants', true);
        $this->insertPlantInPlantRelatedTable("plants", 5, "name", 0, true, null);
        $this->insertPlantInPlantRelatedTable("plants", 5, "soft_deleted", 5, true, now());
        $collection = $this->syncLogic->syncPlants('plants');
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

}
