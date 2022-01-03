<?php

namespace JohanKladder\Stadia\Console;

use Illuminate\Console\Command;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;

class CalendarRangesUpdate extends Command
{

    protected $signature = 'stadia:update-ranges';

    protected $description = 'Update the ranges to the current year';


    public function handle()
    {
        $this->info("Trying to update all the ranges to the current year...");

        foreach(StadiaPlantCalendarRange::all() as $range) {
            $fromDate = $range->range_from;
            $toDate = $range->range_to;
            $fromDate->year = now()->year;
            $toDate->year = now()->year;
            $range->update([
                'range_from' => $fromDate,
                'range_to' => $toDate
            ]);
        }
    }
}
