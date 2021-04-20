<?php


namespace JohanKladder\Stadia\Tests;

use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;

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
}
