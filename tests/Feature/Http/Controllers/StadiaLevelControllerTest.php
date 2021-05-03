<?php


namespace JohanKladder\Stadia\Tests\Feature\Http\Controllers;


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
        $response = $this->get("stadia/stadia-levels/index-with-plant/" . $this->createStadiaPlant()->id);
        $response->assertOk();
        $response->assertViewIs('stadia::stadia-levels.index');
        $response->assertViewHas(['items']);
        $items = $response->viewData('items');
        $this->assertCount(0, $items);
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

}
