<?php


namespace JohanKladder\Stadia\Tests\Feature\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaLevelControllerTest extends TestCase
{

    /** @test */
    public function index_with_unknown_plant()
    {
        $response = $this->get("stadia/stadia-levels/index-with-plant/123");
        $response->assertNotFound();
    }

    /** @test */
    public function index_with_plant_when_no_stadia_levels()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $response = $this->get("stadia/stadia-levels/index-with-plant/" . $stadiaPlant->id);
        $response->assertOk();
        $response->assertViewIs('stadia::stadia-levels.index');
        $response->assertViewHas(['items', 'stadiaPlant']);
        $items = $response->viewData('items');
        $plant = $response->viewData('stadiaPlant');
        $this->assertCount(0, $items);
        $this->assertEquals($stadiaPlant->id, $plant->id);
    }

    /** @test */
    public function index_with_plant_when_stadia_levels()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel('Test', $stadiaPlant);
        $response = $this->get("stadia/stadia-levels/index-with-plant/" . $stadiaPlant->id);
        $response->assertOk();
        $response->assertViewIs('stadia::stadia-levels.index');
        $response->assertViewHas(['items']);

        $items = $response->viewData('items');
        $this->assertCount(1, $items);
        $this->assertEquals($stadiaLevel->id, $items[0]->id);
    }

    /** @test */
    public function remove_stadia_level()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $response = $this->delete(route('stadia-levels.destroy', [
            $this->createStadiaLevel("test", $stadiaPlant)->id
        ]));
        $response->assertRedirect();
        $response->assertSessionHas('message', "Level deleted!");
        $response->assertSessionHas('alert', "alert-success");
        $this->assertDatabaseCount('stadia_levels', 0);
    }

    /** @test */
    public function remove_unknown_stadia_level()
    {

        $response = $this->delete(route('stadia-levels.destroy', [
            'stadia_level' => 123
        ]));
        $response->assertNotFound();
    }


    /** @test */
    public function sync_without_table_given()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $response = $this->get(route("stadia-levels.sync", $stadiaPlant));
        $response->assertRedirect(route("stadia-levels.index", $stadiaPlant));
        $response->assertSessionHas('message', "Could'nt sync items! SQLSTATE[HY000]: General error: 1 no such table: levels (SQL: select * from levels)");
        $response->assertSessionHas('alert', "alert-danger");
    }

    /** @test */
    public function sync_with_not_existing_table_given()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $this->createLevelRelatedTable('wrong-table-name');
        $response = $this->get(route("stadia-levels.sync", $stadiaPlant));
        $response->assertRedirect(route("stadia-levels.index", $stadiaPlant));
        $response->assertSessionHas('message', "Could'nt sync items! SQLSTATE[HY000]: General error: 1 no such table: levels (SQL: select * from levels)");
        $response->assertSessionHas('alert', "alert-danger");
    }

    /** @test */
    public function sync_with_existing_table_but_no_items()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $this->createLevelRelatedTable('levels');
        $response = $this->get(route("stadia-levels.sync", $stadiaPlant));
        $response->assertRedirect(route("stadia-levels.index", $stadiaPlant));
        $response->assertSessionHas('message', "Sync completed - added 0 items!");
        $response->assertSessionHas('alert', "alert-success");
    }

    /** @test */
    public function sync_with_existing_table_with_items()
    {
        $stadiaPlant = StadiaPlant::create([
            'name' => 'Test',
            'reference_id' => 0,
        ]);
        $this->createLevelRelatedTable('levels');
        $this->insertLevelInLevelRelatedTable('levels');
        $response = $this->get(route("stadia-levels.sync", $stadiaPlant));
        $response->assertRedirect(route("stadia-levels.index", $stadiaPlant));
        $response->assertSessionHas('message', "Sync completed - added 5 items!");
        $response->assertSessionHas('alert', "alert-success");
        $this->assertCount(5, StadiaLevel::all());
    }


    private function createStadiaPlant($name = 'Testname')
    {
        return StadiaPlant::create([
            'name' => $name
        ]);
    }

    private function createStadiaLevel($name = 'Testname', StadiaPlant $stadiaPlant = null)
    {
        return StadiaLevel::create([
            'name' => $name,
            'stadia_plant_id' => $stadiaPlant ? $stadiaPlant->id : null
        ]);
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
