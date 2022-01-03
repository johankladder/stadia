<?php

namespace JohanKladder\Stadia\Tests\Console;

use JohanKladder\Stadia\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;

class CalendarRangesUpdateTest extends TestCase
{

    public function test_updating_year_when_previous_years()
    {
        $rangeDate = now();
        $rangeDate->year = now()->year - 1;
        $range = StadiaPlantCalendarRange::create([
            'range_from' => $rangeDate,
            'range_to' => $rangeDate,
        ]);
        Artisan::call("stadia:update-ranges");
        $this->assertEquals(now()->year, $range->refresh()->range_from->year);
        $this->assertEquals(now()->year, $range->refresh()->range_to->year);
    }

    public function test_not_updating_year_when_current_years()
    {
        $rangeDate = now();
        $range = StadiaPlantCalendarRange::create([
            'range_from' => $rangeDate,
            'range_to' => $rangeDate,
        ]);
        Artisan::call("stadia:update-ranges");
        $this->assertEquals($range->range_from->year, $rangeDate->year);
        $this->assertEquals($range->range_to->year, $rangeDate->year);
    }
}
