<?php


namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;


use JohanKladder\Stadia\Exceptions\NoStadiaLevelFoundException;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\Information\KoepenLocation;
use JohanKladder\Stadia\Models\Information\StadiaLevelInformation;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\Wrappers\LocationWrapper;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeLevelInformationFeatureTest extends TestCase
{

    /** @test */
    public function store_level_information_not_existing_reference()
    {
        $this->expectException(NoStadiaLevelFoundException::class);
        Stadia::storeLevelInformation(
            1,
            now(),
            now()
        );
    }

    /** @test */
    public function store_level_information_happy_flow_without_location_information()
    {
        $stadiaLevel = $this->createStadiaLevel(1);

        Stadia::storeLevelInformation(
            1,
            now(),
            now()
        );

        $this->assertDatabaseHas('stadia_level_information', [
            'stadia_level_id' => $stadiaLevel->id,
            'start_date' => now(),
            'end_date' => now(),
            'country_id' => null,
            'climate_code_id' => null
        ]);
    }

    /** @test */
    public function store_level_information_happy_flow_with_location_information()
    {
        $stadiaLevel = $this->createStadiaLevel(1);
        $country = Country::create([
            'code' => 'NL'
        ]);

        KoepenLocation::firstOrCreate([
            'latitude' => -89.75,
            'longitude' => -179.75,
            'code' => 'Ca'
        ]);

        $climateCode = ClimateCode::create([
            'code' => 'Ca'
        ]);

        Stadia::storeLevelInformation(
            1,
            now(),
            now(),
            new LocationWrapper(
                $country->code,
                -89.75,
                -179.75,
            )
        );

        $this->assertDatabaseHas('stadia_level_information', [
            'stadia_level_id' => $stadiaLevel->id,
            'start_date' => now(),
            'end_date' => now(),
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);
    }

    /** @test */
    public function get_level_information_empty()
    {
        $items = Stadia::getLevelInformation($this->createStadiaLevel(1));
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_level_information_with_stadia_level()
    {
        $stadiaLevel = $this->createStadiaLevel(1);
        StadiaLevelInformation::create([
            'stadia_level_id' => $stadiaLevel->id,
            'start_date' => now(),
            'end_date' => now()
        ]);
        $items = Stadia::getLevelInformation($stadiaLevel);
        $this->assertCount(1, $items);
    }

    /** @test */
    public function get_level_information_with_stadia_level_and_location()
    {
        $stadiaLevel = $this->createStadiaLevel(1);
        $country = Country::create([
            'code' => 'NL'
        ]);
        $climateCode = ClimateCode::create([
            'code' => 'Ca'
        ]);
        StadiaLevelInformation::create([
            'stadia_level_id' => $stadiaLevel->id,
            'start_date' => now(),
            'end_date' => now(),
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);
        $items = Stadia::getLevelInformation($stadiaLevel,
            new LocationWrapper(
                $country->code,
                -89.75,
                -179.75,
            ));
        $this->assertCount(1, $items);
    }

    /** @test */
    public function get_level_information_only_global_when_both()
    {

        $stadiaLevel = $this->createStadiaLevel(1);
        $country = Country::create([
            'code' => 'NL'
        ]);
        $climateCode = ClimateCode::create([
            'code' => 'Ca'
        ]);
        StadiaLevelInformation::create([
            'stadia_level_id' => $stadiaLevel->id,
            'start_date' => now(),
            'end_date' => now(),
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);

        $correct = StadiaLevelInformation::create([
            'stadia_level_id' => $stadiaLevel->id,
            'start_date' => now(),
            'end_date' => now()
        ]);

        $items = Stadia::getLevelInformation($stadiaLevel);
        $this->assertCount(1, $items);
        $this->assertEquals($correct->id, $items[0]->id);
    }

    /** @test */
    public function get_level_information_only_location_when_both()
    {
        $stadiaLevel = $this->createStadiaLevel(1);
        $country = Country::create([
            'code' => 'NL'
        ]);
        $climateCode = ClimateCode::create([
            'code' => 'Ca'
        ]);
        $correct = StadiaLevelInformation::create([
            'stadia_level_id' => $stadiaLevel->id,
            'start_date' => now(),
            'end_date' => now(),
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);

        StadiaLevelInformation::create([
            'stadia_level_id' => $stadiaLevel->id,
            'start_date' => now(),
            'end_date' => now()
        ]);

        $items = Stadia::getLevelInformation($stadiaLevel,
            new LocationWrapper(
                $country->code,
                -89.75,
                -179.75,
            ));
        $this->assertCount(1, $items);
        $this->assertEquals($correct->id, $items[0]->id);
    }

    private function createStadiaLevel($referenceId): StadiaLevel
    {
        return StadiaLevel::create([
            'name' => 'Test Level',
            'reference_id' => $referenceId
        ]);
    }

}
