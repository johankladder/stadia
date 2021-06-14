<?php


namespace JohanKladder\Stadia\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use JohanKladder\Stadia\Models\Information\StadiaLevelInformation;

class LevelChart extends BaseChart
{

    /**
     * @inheritDoc
     */
    public function handler(Request $request): Chartisan
    {
        $monthlyCounts = $this->getMontlyCounts();
        return Chartisan::build()
            ->labels($this->getMonthLabels())
            ->dataset('Entries', $monthlyCounts)
            ->dataset('Average', $this->getMonthlyAverage($monthlyCounts));
    }

    private function getMonthLabels(): array
    {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
        }
        return $months;
    }

    private function getMontlyCounts(): array
    {
        $montlyCounts = [];

        for ($m = 1; $m <= 12; $m++) {
            $montlyCounts[] = StadiaLevelInformation::whereMonth('created_at', $m)
                ->whereYear('created_at', now()->year)
                ->count();
        }

        return $montlyCounts;
    }

    private function getMonthlyAverage(array $monthlyCounts): array
    {
        $sum = 0;
        foreach ($monthlyCounts as $monthlyCount) {
            $sum += $monthlyCount;
        }

        $average = $sum / count($monthlyCounts);

        return array_fill(0, count($monthlyCounts), $average);
    }
}
