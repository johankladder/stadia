<?php


namespace JohanKladder\Stadia\Tests\Feature\Http\Controllers;


use Illuminate\Support\Collection;
use JohanKladder\Stadia\Models\Information\StadiaHarvestInformation;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;

class UserInformationControllerTest extends TestCase
{

    /** @test */
    public function get_index_when_no_plants()
    {
        $response = $this->get("stadia/user-information");
        $response->assertOk();
        $response->assertViewIs('stadia::user-information.index');
        $response->assertViewHas(['mostHarvest']);

        $mostHarvest = $response->viewData('mostHarvest');
        $this->assertCount(0, $mostHarvest);
    }

    /** @test */
    public function get_index_when_less_plants_then_5()
    {
        $this->createStadiaPlants(2);

        $response = $this->get("stadia/user-information");
        $response->assertOk();
        $response->assertViewIs('stadia::user-information.index');
        $response->assertViewHas(['mostHarvest']);

        $mostHarvest = $response->viewData('mostHarvest');
        $this->assertCount(0, $mostHarvest);
    }

    /** @test */
    public function get_index_when_nothing_harvest()
    {
        $this->createStadiaPlants(5);

        $response = $this->get("stadia/user-information");
        $response->assertOk();
        $response->assertViewIs('stadia::user-information.index');
        $response->assertViewHas(['mostHarvest']);

        $mostHarvest = $response->viewData('mostHarvest');
        $this->assertCount(0, $mostHarvest);

        foreach ($mostHarvest as $item) {
            $this->assertEquals(0, $item->harvest_count);
        }
    }

    /** @test */
    public function get_index_happy_flow()
    {
        $this->createHarvestInformation($this->createStadiaPlants(5), 5);

        $response = $this->get("stadia/user-information");
        $response->assertOk();
        $response->assertViewIs('stadia::user-information.index');
        $response->assertViewHas(['mostHarvest']);

        $mostHarvest = $response->viewData('mostHarvest');
        $this->assertCount(5, $mostHarvest);

        foreach ($mostHarvest as $item) {
            $this->assertEquals(5, $item->harvest_count);
        }
    }

    /** @test */
    public function get_index_happy_flow_more_then_5_different_count()
    {

        $this->createHarvestInformation($this->createStadiaPlants(1), 10);
        $this->createHarvestInformation($this->createStadiaPlants(1), 8);

        $this->createHarvestInformation($this->createStadiaPlants(5), 5);


        $response = $this->get("stadia/user-information");
        $response->assertOk();
        $response->assertViewIs('stadia::user-information.index');
        $response->assertViewHas(['mostHarvest']);

        $mostHarvest = $response->viewData('mostHarvest');
        $this->assertCount(5, $mostHarvest);

        $this->assertEquals(10, $mostHarvest[0]->harvest_count);
        $this->assertEquals(8, $mostHarvest[1]->harvest_count);
        $this->assertEquals(5, $mostHarvest[2]->harvest_count);
        $this->assertEquals(5, $mostHarvest[3]->harvest_count);
        $this->assertEquals(5, $mostHarvest[4]->harvest_count);
    }

    private function createStadiaPlants($n = 1)
    {
        $stadiaPlants = Collection::make();
        for ($i = 0; $i < $n; $i++) {
            $stadiaPlants->add(StadiaPlant::create());
        }
        return $stadiaPlants;
    }

    private function createHarvestInformation(Collection $stadiaPlants, $n = 1)
    {
        foreach ($stadiaPlants as $stadiaPlant) {
            for ($i = 0; $i < $n; $i++) {
                StadiaHarvestInformation::create([
                    'stadia_plant_id' => $stadiaPlant->id,
                    'sow_date' => now(),
                    'harvest_date' => now()
                ]);
            }
        }
    }


}
