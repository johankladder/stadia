<?php


namespace JohanKladder\Stadia\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;

class CalendarControllerFeatureTest extends TestCase
{

    /** @test */
    public function sync_without_table_given()
    {
        $response = $this->get(route("stadia-plants.sync"));
        $response->assertRedirect(route("stadia-plants.index"));
        $response->assertSessionHas('message', "Could'nt sync items! SQLSTATE[HY000]: General error: 1 no such table: plants (SQL: select * from plants)");
        $response->assertSessionHas('alert', "alert-danger");
    }

    /** @test */
    public function sync_with_not_existing_table_given()
    {
        $this->createPlantRelatedTable('wrong-table-name');
        $response = $this->get(route("stadia-plants.sync"));
        $response->assertRedirect(route("stadia-plants.index"));
        $response->assertSessionHas('message', "Could'nt sync items! SQLSTATE[HY000]: General error: 1 no such table: plants (SQL: select * from plants)");
        $response->assertSessionHas('alert', "alert-danger");
    }

    /** @test */
    public function sync_with_existing_table_but_no_items()
    {
        $this->createPlantRelatedTable('plants');
        $response = $this->get(route("stadia-plants.sync"));
        $response->assertRedirect(route("stadia-plants.index"));
        $response->assertSessionHas('message', "Sync completed - added 0 items!");
        $response->assertSessionHas('alert', "alert-success");
    }

    /** @test */
    public function sync_with_existing_table_with_items()
    {
        $this->createPlantRelatedTable('plants');
        $this->insertPlantInPlantRelatedTable('plants');
        $response = $this->get(route("stadia-plants.sync"));
        $response->assertRedirect(route("stadia-plants.index"));
        $response->assertSessionHas('message', "Sync completed - added 5 items!");
        $response->assertSessionHas('alert', "alert-success");

        $this->assertCount(5, StadiaPlant::all());
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

    public function insertPlantInPlantRelatedTable($tableName, $n = 5, $fromId = 0)
    {
        for ($i = 0; $i < $n; $i++) {
            DB::insert("insert into " . $tableName . " (id, name) values (?, ?)", [($i + $fromId), 'Name']);
        }
    }

}
