<?php


namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;


use JohanKladder\Stadia\Exceptions\NoStadiaPlantFoundException;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeHarvestInformationFeatureTest extends TestCase
{

    /** @test */
    public function store_harvest_information_not_existing_reference()
    {
        $this->expectException(NoStadiaPlantFoundException::class);
        Stadia::storeHarvestInformation(
            1,
            now(),
            now()
        );

        $this->assertDatabaseMissing('stadia_harvest_information', [
            'stadia_plant_id' => 1
        ]);
    }

    /** @test */
    public function store_harvest_information_happy_flow_without_location_information()
    {
        $stadiaPlant = StadiaPlant::create([
            'reference_id' => 1,
            'reference_table' => 'plants'
        ]);
        Stadia::storeHarvestInformation(
            $stadiaPlant->reference_id,
            now(),
            now(),
        );

        $this->assertDatabaseHas('stadia_harvest_information', [
            'stadia_plant_id' => $stadiaPlant->id,
            'sow_date' => now(),
            'harvest_date' => now(),
            'country_id' => null,
            'climate_code_id' => null
        ]);
    }

    /** @test */
    public function store_harvest_information_happy_flow_with_location_information()
    {
        $stadiaPlant = StadiaPlant::create([
            'reference_id' => 1,
            'reference_table' => 'plants'
        ]);

        $country = Country::create([
            'code' => 'NL'
        ]);

        $climateCode = ClimateCode::create([
            'code' => 'Cc'
        ]);

        Stadia::storeHarvestInformation(
            $stadiaPlant->reference_id,
            now(),
            now(),
            $country->code,
            $climateCode->code
        );

        $this->assertDatabaseHas('stadia_harvest_information', [
            'stadia_plant_id' => $stadiaPlant->id,
            'sow_date' => now(),
            'harvest_date' => now(),
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);
    }

}
