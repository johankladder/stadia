<?php


namespace JohanKladder\Stadia\Tests\Feature\Logic;


use Illuminate\Database\QueryException;
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

    private function createPlantRelatedTable($tableName)
    {
        Schema::create($tableName, function ($table) {
            $table->temporary();
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function insertPlantInPlantRelatedTable($tableName, $n = 5, $name = "Name", $fromId = 0)
    {
        for ($i = 0; $i < $n; $i++) {
            DB::insert("insert into " . $tableName . " (id, name) values (?, ?)", [($i + $fromId), $name]);
        }
    }

}
