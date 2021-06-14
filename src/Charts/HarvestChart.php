<?php


namespace JohanKladder\Stadia\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use JohanKladder\Stadia\Models\Information\StadiaHarvestInformation;

class HarvestChart extends BaseChart
{

    /**
     * @inheritDoc
     */
    public function handler(Request $request): Chartisan
    {
        $monthlyCounts = $this->getMonthlyCounts();
        return Chartisan::build()
            ->labels($this->getMonthLabels())
            ->dataset('Entries', $monthlyCounts)
            ->dataset('Avarage', $this->getAvarageLineCounts($monthlyCounts));
    }

    private function getMonthLabels(): array
    {
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
        }
        return $months;
    }

    private function getMonthlyCounts(): array
    {
        $monthlyCounts = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlyCounts[] = StadiaHarvestInformation::whereMonth('created_at', $m)->count();
        }

        return $monthlyCounts;
    }

    private function getAvarageLineCounts(array $monthlyCounts): array
    {
        $sum = 0;

        foreach ($monthlyCounts as $count) {
            $sum += $count;
        }

        $average = $sum / count($monthlyCounts);

        $averageLineValues = [];

        for ($m = 1; $m <= 12; $m++) {
            $averageLineValues[] = $average;
        }

        return $averageLineValues;
    }
}
