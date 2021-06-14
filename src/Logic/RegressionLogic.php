<?php


namespace JohanKladder\Stadia\Logic;


use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
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
        $regressor = new LeastSquares();

        if ($harvestInformations->isNotEmpty()) {
            [$samples, $targets] = $this->createSamplesAndTargets(
                $harvestInformations,
                'sow_date',
                'harvest_date'
            );

            $dataset = new ArrayDataset(
                $samples,
                $targets
            );

            $regressor = $this->trainRegressor(
                $dataset,
                $regressor
            );
        }

        return $regressor;
    }

    public function createAndTrainLevelPrediction(Collection $levelInformations)
    {
        $regressor = new LeastSquares();

        if ($levelInformations->isNotEmpty()) {
            [$samples, $targets] = $this->createSamplesAndTargets(
                $levelInformations,
                'start_date',
                'end_date'
            );

            $dataset = new ArrayDataset(
                $samples,
                $targets
            );

            $regressor = $this->trainRegressor(
                $dataset,
                $regressor
            );
        }

        return $regressor;
    }

    private function createSamplesAndTargets(Collection $items, string $fromKey, string $toKey)
    {
        $samples = [];
        $targets = [];
        foreach ($items as $item) {
            $startDate = $item[$fromKey]->dayOfYear;
            $samples[] = [$startDate];
            $duration = $item[$toKey]->diffInDays($item[$fromKey]);
            $targets[] = $startDate + $duration;
        }

        return [
            $samples,
            $targets
        ];

    }

    private function trainRegressor(ArrayDataset $dataset, LeastSquares $regressor): LeastSquares
    {
        $regressor->train($dataset->getSamples(), $dataset->getTargets());
        return $regressor;
    }

    public function getYCoordinateBestFit($x, $slope, $intercept)
    {
        return ($slope * $x) + $intercept;
    }
}
