<?php


namespace JohanKladder\Stadia\Logic;


use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Phpml\Regression\LeastSquares;

class RegressionLogic
{

    public function predictHarvestDay(LeastSquares $regression, CarbonInterface $startTime): ?int
    {
        return $regression->predict([$startTime->dayOfYear]);
    }

    public function createAndTrainHarvestPrediction(Collection $harvestInformations)
    {
        $regression = new LeastSquares();

        if ($harvestInformations->isNotEmpty()) {
            $samples = [];
            $targets = [];
            foreach ($harvestInformations as $harvestInformation) {
                $sowDay = $harvestInformation->sow_date->dayOfYear;
                $samples[] = [$sowDay];
                $duration = $harvestInformation->harvest_date->diffInDays($harvestInformation->sow_date);
                $targets[] = $sowDay + $duration;
            }
            $regression->train($samples, $targets);
        }

        return $regression;
    }

    public function getYCoordinateBestFit($x, $slope, $intercept)
    {
        return ($slope * $x) + $intercept;
    }


}
