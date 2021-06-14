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
                $samples[] = [$harvestInformation->sow_date->dayOfYear];
                $targets[] = $harvestInformation->harvest_date->dayOfYear;
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
