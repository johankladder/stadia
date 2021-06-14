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
        $dailyCounts = $this->getDailyCounts();
        return Chartisan::build()
            ->labels($this->getDayLabels())
            ->dataset('Entries', $dailyCounts)
            ->dataset('Average', $this->getDailyAverage($dailyCounts));
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

    private function getDailyAverage(array $dailyCounts): array
    {
        $sum = 0;
        foreach ($dailyCounts as $dailyCount) {
            $sum += $dailyCount;
        }

        $average = $sum / count($dailyCounts);

        return array_fill(0, count($dailyCounts), $average);
    }
}
