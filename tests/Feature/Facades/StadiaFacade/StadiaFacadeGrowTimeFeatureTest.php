<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;

use JohanKladder\Stadia\Exceptions\NoStadiaLevelsException;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaLevelDuration;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeGrowTimeFeatureTest extends TestCase
{


    /** @test */
    public function get_grow_time_no_levels()
    {
        $this->expectException(NoStadiaLevelsException::class);
        $stadiaPlant = $this->createStadiaPlant();
        Stadia::getGrowTime($stadiaPlant);
    }

    /** @test */
    public function get_grow_time_has_levels_no_durations()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $this->createStadiaLevel($stadiaPlant);
        $growTime = Stadia::getGrowTime($stadiaPlant);
        $this->assertEquals(0, $growTime);
    }

    /** @test */
    public function get_grow_time_has_levels_only_global()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $stadiaDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $growTime = Stadia::getGrowTime($stadiaPlant);
        $this->assertEquals($stadiaDuration->duration, $growTime);
    }

    /** @test */
    public function get_grow_time_has_levels_only_global_multiple_levels()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $stadiaLevel2 = $this->createStadiaLevel($stadiaPlant);
        $stadiaDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $this->createStadiaDuration($stadiaLevel2, 5);
        $growTime = Stadia::getGrowTime($stadiaPlant);
        $this->assertEquals($stadiaDuration->duration * 2, $growTime);
    }


    /** @test */
    public function get_grow_time_has_levels_global_and_country_based()
    {
        $country = $this->createCountry();
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $globalDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $countryDuration = $this->createStadiaDuration($stadiaLevel, 10, $country);
        $growTime = Stadia::getGrowTime($stadiaPlant, $country);
        $this->assertEquals($countryDuration->duration, $growTime);
        $this->assertNotEquals($globalDuration->duration, $growTime);
    }

    /** @test */
    public function get_grow_time_has_levels_country_and_climate_codes_based()
    {
        $country = $this->createCountry();
        $climate = $this->createClimateCode();
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $countryDuration = $this->createStadiaDuration($stadiaLevel, 5, $country);
        $climateDuration = $this->createStadiaDuration($stadiaLevel, 10, $country, $climate);
        $growTime = Stadia::getGrowTime($stadiaPlant, $country, $climate);
        $this->assertEquals($climateDuration->duration, $growTime);
        $this->assertNotEquals($countryDuration->duration, $growTime);
    }


    private function createStadiaPlant($refId = 1)
    {
        return StadiaPlant::create([
            'reference_id' => $refId,
            'reference_table' => 'plants'
        ]);
    }

    private function createStadiaLevel(StadiaPlant $stadiaPlant = null)
    {
        return StadiaLevel::create([
            'name' => 'Level',
            'stadia_plant_id' => $stadiaPlant ? $stadiaPlant->id : $this->createStadiaPlant()->id
        ]);
    }

    private function createStadiaDuration(StadiaLevel $stadiaLevel, $duration, $country = null, $climate = null)
    {
        return StadiaLevelDuration::create([
            'stadia_level_id' => $stadiaLevel->id,
            'duration' => $duration,
            'country_id' => $country ? $country->id : null,
            'climate_code_id' => $climate ? $climate->id : null,
        ]);
    }


    private function createCountry()
    {
        return Country::create();
    }

    private function createClimateCode()
    {
        return ClimateCode::create([
            'code' => 'test'
        ]);
    }

}
