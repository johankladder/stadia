<?php


namespace JohanKladder\Stadia\Tests\Models;

use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaPlantUnitTest extends TestCase
{

    /** @test */
    public function test_get_id()
    {
        $sp = StadiaPlant::create([]);
        $this->assertNotNull($sp->id);
    }

    public function test_get_calendar_ranges_when_none()
    {
        $sp = StadiaPlant::create();
        $this->assertCount(0, $sp->calendarRanges);
    }

    public function test_get_calendar_ranged_when_any()
    {
        $sp = StadiaPlant::create();
        StadiaPlantCalendarRange::create([
            'range_from' => now(),
            'range_to' => now(),
            'stadia_plant_id' => $sp->id
        ]);
        $this->assertCount(1, $sp->calendarRanges);
    }

}
