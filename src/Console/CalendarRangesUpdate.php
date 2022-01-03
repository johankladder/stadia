<?php

namespace JohanKladder\Stadia\Console;

use Illuminate\Console\Command;

class CalendarRangesUpdate extends Command
{

    protected $signature = 'stadia:update-ranges';

    protected $description = 'Update the ranges to the current year';


    public function handle()
    {
        $this->info("Trying to update all the ranges to the current year...");
    }
}
