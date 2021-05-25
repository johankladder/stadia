<?php


namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;


use JohanKladder\Stadia\Exceptions\NoStadiaPlantFoundException;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\Information\StadiaHarvestInformation;
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

    /** @test */
    public function get_harvest_information_empty()
    {
        $items = Stadia::getHarvestInformation(StadiaPlant::create());
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_harvest_information_with_stadia_plant()
    {
        $stadiaPlant = StadiaPlant::create();
        StadiaHarvestInformation::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'sow_date' => now(),
            'harvest_date' => now()
        ]);
        $items = Stadia::getHarvestInformation($stadiaPlant);
        $this->assertCount(1, $items);
    }

    /** @test */
    public function get_harvest_information_with_stadia_plant_and_location()
    {
        $stadiaPlant = StadiaPlant::create();
        $country = Country::create([
            'code' => 'NL'
        ]);
        $climateCode = ClimateCode::create([
            'code' => "CC"
        ]);
        StadiaHarvestInformation::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'sow_date' => now(),
            'harvest_date' => now(),
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);
        $items = Stadia::getHarvestInformation($stadiaPlant, $country, $climateCode);
        $this->assertCount(1, $items);
    }

    /** @test */
    public function get_harvest_information_only_global_when_both()
    {

        $stadiaPlant = StadiaPlant::create();
        $country = Country::create([
            'code' => 'NL'
        ]);
        $climateCode = ClimateCode::create([
            'code' => "CC"
        ]);
        StadiaHarvestInformation::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'sow_date' => now(),
            'harvest_date' => now(),
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);

        $correct = StadiaHarvestInformation::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'sow_date' => now(),
            'harvest_date' => now()
        ]);

        $items = Stadia::getHarvestInformation($stadiaPlant);
        $this->assertCount(1, $items);
        $this->assertEquals($correct->id, $items[0]->id);
    }

    /** @test */
    public function get_harvest_information_only_location_when_both()
    {
        $stadiaPlant = StadiaPlant::create();
        $country = Country::create([
            'code' => 'NL'
        ]);
        $climateCode = ClimateCode::create([
            'code' => "CC"
        ]);
        $correct = StadiaHarvestInformation::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'sow_date' => now(),
            'harvest_date' => now(),
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);

        StadiaHarvestInformation::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'sow_date' => now(),
            'harvest_date' => now()
        ]);

        $items = Stadia::getHarvestInformation($stadiaPlant, $country, $climateCode);
        $this->assertCount(1, $items);
        $this->assertEquals($correct->id, $items[0]->id);
    }

}
