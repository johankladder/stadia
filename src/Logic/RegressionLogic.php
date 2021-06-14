<?php


namespace JohanKladder\Stadia\Logic;


use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Dataset\ArrayDataset;
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

            $dataset = new ArrayDataset(
                $samples,
                $targets
            );

            $dataset = new StratifiedRandomSplit($dataset, 0.3);

            [$xTrain, $xTest, $yTrain, $yTest] = [
                $dataset->getTrainSamples(),
                $dataset->getTestSamples(),
                $dataset->getTrainLabels(),
                $dataset->getTestLabels()
            ];

            $regression->train($samples, $targets);
        }

        return $regression;
    }

    public function getYCoordinateBestFit($x, $slope, $intercept)
    {
        return ($slope * $x) + $intercept;
    }
}
