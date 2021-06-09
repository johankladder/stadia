<?php

namespace JohanKladder\Stadia\Database\Seeds;


use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use JohanKladder\Stadia\Models\Information\KoepenLocation;

class KoepenLocationTableSeeder extends Seeder
{

    public function run()
    {
        KoepenLocation::truncate();
        $data = Collection::make();
        $lineNumber = 0;

        $handle = fopen(__DIR__ . "/datasets/koepen-dataset.txt", "r");
        if ($handle) {

            while (($line = fgets($handle)) !== false) {
                if ($lineNumber > 0) {
                    [$lat, $lon, $code] = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
                    $data->add([
                        'latitude' => $lat,
                        'longitude' => $lon,
                        'code' => $code
                    ]);
                }
                $lineNumber++;
            }

            $this->insertCollection($data, 500);

            fclose($handle);
        }
    }

    private function insertCollection(Collection $collection, $chuckSize = 500)
    {
        $chunks = $collection->chunk(500);
        foreach ($chunks as $chunk) {
            KoepenLocation::insert($chunk->toArray());
        }
    }


}
