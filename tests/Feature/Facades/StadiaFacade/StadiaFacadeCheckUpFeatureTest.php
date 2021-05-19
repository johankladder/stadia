<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;

use JohanKladder\Stadia\Exceptions\NoDurationsException;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaLevelDuration;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeCheckUpFeatureTest extends TestCase
{

    /** @test */
    public function get_check_up_date_no_durations()
    {
        $this->expectException(NoDurationsException::class);
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        Stadia::getCheckupDate($stadiaLevel);
    }

    /** @test */
    public function get_check_up_date_when_duration()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $duration = $this->createStadiaDuration($stadiaLevel, 5);
        $newTime = Stadia::getCheckupDate($stadiaLevel);
        $this->assertEquals(now()->addDays($duration->duration)->roundDay(), $newTime);
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


}
