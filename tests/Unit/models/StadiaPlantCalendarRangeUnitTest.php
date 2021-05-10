<?php


namespace JohanKladder\Stadia\Tests\Models;

use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaPlantCalendarRangeUnitTest extends TestCase
{

    public function test_get_ranges()
    {
        $range = StadiaPlantCalendarRange::create([
            'range_from' => now(),
            'range_to' => now()
        ]);

        $this->assertNotNull($range->range_from);
        $this->assertNotNull($range->range_to);
    }

    public function test_get_country_id_when_none()
    {
        $range = StadiaPlantCalendarRange::create([
            'range_from' => now(),
            'range_to' => now(),
        ]);
        $this->assertNull($range->country_id);
    }

    public function test_get_country_id()
    {
        $range = StadiaPlantCalendarRange::create([
            'range_from' => now(),
            'range_to' => now(),
            'country_id' => 1
        ]);
        $this->assertEquals(1, $range->country_id);
    }

    public function test_get_climate_code_id()
    {
        $range = StadiaPlantCalendarRange::create([
            'range_from' => now(),
            'range_to' => now(),
            'climate_code_id' => 1
        ]);
        $this->assertEquals(1, $range->climate_code_id);
    }
}
