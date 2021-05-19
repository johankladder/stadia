<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;

use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaLevelDuration;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeFeatureTest extends TestCase
{


    /** @test */
    public function gather_all_stadia_plants_when_none()
    {
        $items = Stadia::getAllPlants();
        $this->assertCount(0, $items);
    }

    /** @test */
    public function gather_all_stadia_plants()
    {
        $this->createStadiaPlant();
        $items = Stadia::getAllPlants();
        $this->assertCount(1, $items);
    }

    /** @test */
    public function gather_all_countries_when_none()
    {
        $items = Stadia::getAllCountries();
        $this->assertCount(0, $items);
    }

    /** @test */
    public function gather_all_countries()
    {
        $this->createCountry();
        $items = Stadia::getAllCountries();
        $this->assertCount(1, $items);
    }

    private function createStadiaPlant($refId = 1)
    {
        return StadiaPlant::create([
            'reference_id' => $refId,
            'reference_table' => 'plants'
        ]);
    }

    private function createCountry()
    {
        return Country::create();
    }

}
