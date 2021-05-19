<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;

use JohanKladder\Stadia\Exceptions\NoDurationsException;
use JohanKladder\Stadia\Exceptions\NoStadiaLevelsException;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaLevelDuration;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeDurationFeatureTest extends TestCase
{


    /** @test */
    public function get_duration_when_no_global_durations()
    {
        $this->expectException(NoDurationsException::class);
        $stadiaLevel = $this->createStadiaLevel();
        Stadia::getDuration($stadiaLevel);
    }

    /** @test */
    public function get_duration_when_global_durations()
    {
        $stadiaLevel = $this->createStadiaLevel();
        $levelDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $duration = Stadia::getDuration($stadiaLevel);
        $this->assertEquals($levelDuration->duration, $duration);
    }

    /** @test */
    public function get_duration_when_global_and_country_durations()
    {
        $country = $this->createCountry();
        $stadiaLevel = $this->createStadiaLevel();
        $globalDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $countryDuration = $this->createStadiaDuration($stadiaLevel, 10, $country);
        $duration = Stadia::getDuration($stadiaLevel, $country);
        $this->assertEquals($countryDuration->duration, $duration);
        $this->assertNotEquals($globalDuration->duration, $duration);
    }

    /** @test */
    public function get_duration_when_country_and_climate_durations()
    {
        $country = $this->createCountry();
        $climate = $this->createClimateCode();
        $stadiaLevel = $this->createStadiaLevel();
        $countryDuration = $this->createStadiaDuration($stadiaLevel, 5, $country);
        $climateDuration = $this->createStadiaDuration($stadiaLevel, 10, $country, $climate);
        $duration = Stadia::getDuration($stadiaLevel, $country, $climate);
        $this->assertEquals($climateDuration->duration, $duration);
        $this->assertNotEquals($countryDuration->duration, $duration);
    }

    /** @test */
    public function get_durations_when_no_levels()
    {
        $this->expectException(NoStadiaLevelsException::class);
        $stadiaPlant = $this->createStadiaPlant();
        Stadia::getDurations($stadiaPlant);
    }

    /** @test */
    public function get_durations_happy_flow()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $duration = $this->createStadiaDuration($stadiaLevel, 5);
        $durations = Stadia::getDurations($stadiaPlant);
        $this->assertCount(1, $durations);
        $this->assertEquals($duration->duration, $durations[0]->duration);
    }

    /** @test */
    public function get_durations_happy_flow_multiple_levels()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $stadiaLevel2 = $this->createStadiaLevel($stadiaPlant);
        $duration = $this->createStadiaDuration($stadiaLevel, 5);
        $duration2 = $this->createStadiaDuration($stadiaLevel2, 5);
        $durations = Stadia::getDurations($stadiaPlant);
        $this->assertCount(2, $durations);
        $this->assertEquals($duration->duration, $durations[0]->duration);
        $this->assertEquals($duration2->duration, $durations[1]->duration);
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
