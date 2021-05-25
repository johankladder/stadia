<?php


namespace JohanKladder\Stadia\Tests\Unit\models\Information;


use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\Information\StadiaHarvestInformation;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;
use function PHPUnit\Framework\assertNotNull;

class StadiaHarvestInformationUnitTest extends TestCase
{

    /** @test */
    public function get_plant_id()
    {
        $info = StadiaHarvestInformation::create([
            'stadia_plant_id' => StadiaPlant::create()->id
        ]);
        assertNotNull($info->stadia_plant_id);
    }

    /** @test */
    public function get_country_id()
    {

        $info = StadiaHarvestInformation::create([
            'stadia_plant_id' => StadiaPlant::create()->id,
            'country_id' => Country::create()->id
        ]);
        assertNotNull($info->country_id);
    }

    /** @test */
    public function get_climate_code_id()
    {
        $info = StadiaHarvestInformation::create([
            'stadia_plant_id' => StadiaPlant::create()->id,
            'climate_code_id' => ClimateCode::create([
                'code' => 'NL'
            ])->id
        ]);
        assertNotNull($info->climate_code_id);
    }

    /** @test */
    public function get_sow_date()
    {
        $info = StadiaHarvestInformation::create([
            'stadia_plant_id' => StadiaPlant::create()->id,
            'sow_date' => now()
            ]);
        assertNotNull($info->sow_date);
    }

    /** @test */
    public function get_harvest_date()
    {
        $info = StadiaHarvestInformation::create([
            'stadia_plant_id' => StadiaPlant::create()->id,
            'harvest_date' => now()
        ]);
        assertNotNull($info->harvest_date);
    }

}
