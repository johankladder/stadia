<?php


namespace JohanKladder\Stadia\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use JohanKladder\Stadia\Models\Information\StadiaLevelInformation;

class LevelMonthlyChart extends BaseChart
{

    /**
     * @inheritDoc
     */
    public function handler(Request $request): Chartisan
    {
        return Chartisan::build()
            ->labels($this->getDayLabels())
            ->dataset('Entries', $this->getDailyCounts());
    }

    private function getDayLabels(): array
    {
        $months = [];

        for ($d = 1; $d <= now()->daysInMonth; $d++) {
            $months[] = $d;
        }
        return $months;
    }

    private function getDailyCounts(): array
    {
        $dailyCounts = [];

        for ($day = 1; $day <= now()->daysInMonth; $day++) {
            $dailyCounts[] = StadiaLevelInformation::whereDay('created_at', $day)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        }

        return $dailyCounts;
    }
}
