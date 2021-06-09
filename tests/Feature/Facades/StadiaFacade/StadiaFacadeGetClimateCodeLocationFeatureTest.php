<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;

use JohanKladder\Stadia\Database\Seeds\ClimateCodesTableSeeder;
use JohanKladder\Stadia\Database\Seeds\KoepenLocationTableSeeder;
use JohanKladder\Stadia\Exceptions\ClimateCodeNotFoundException;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Information\KoepenLocation;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeGetClimateCodeLocationFeatureTest extends TestCase
{

    private $latitude = -89.75;
    private $longitude = -179.75;

    private $amsterdamLatitude = 52.377956;
    private $amsterdamLongitude = 4.89707;

    /** @test */
    public function get_climate_code_location_empty_tables()
    {
        $this->expectException(ClimateCodeNotFoundException::class);
        Stadia::getClimateCode(
            $this->latitude,
            $this->longitude
        );
    }

    /** @test */
    public function get_climate_code_location_happy_flow()
    {
        $addedClimateCode = ClimateCode::firstOrCreate([
            'code' => 'EF'
        ]);

        KoepenLocation::firstOrCreate([
            'code' => 'EF',
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ]);

        $climateCode = Stadia::getClimateCode(
            $this->latitude,
            $this->longitude
        );

        $this->assertEquals($addedClimateCode->id, $climateCode->id);
    }

    /** @test */
    public function get_climate_code_location_happy_flow_when_seeded()
    {
        $this->seed(ClimateCodesTableSeeder::class);
        $this->seed(KoepenLocationTableSeeder::class);

        $foundClimateCode = Stadia::getClimateCode(
            $this->amsterdamLatitude,
            $this->amsterdamLongitude
        );
        $this->assertEquals($foundClimateCode->code, 'Cfb');
    }

    /** @test */
    public function get_climate_code_location_happy_flow_rounding()
    {
        $addedClimateCode = ClimateCode::firstOrCreate([
            'code' => 'Cfb'
        ]);

        KoepenLocation::firstOrCreate([
            'code' => 'Cfb',
            'latitude' => 52.25,
            'longitude' => 4.75
        ]);

        $climateCode = Stadia::getClimateCode(
            $this->amsterdamLatitude,
            $this->amsterdamLongitude
        );

        $this->assertEquals($addedClimateCode->id, $climateCode->id);
    }

    /** @test */
    public function get_climate_code_location_not_found()
    {
        $this->expectException(ClimateCodeNotFoundException::class);

        ClimateCode::firstOrCreate([
            'code' => 'EF'
        ]);

        KoepenLocation::firstOrCreate([
            'code' => 'EF',
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ]);

        Stadia::getClimateCode(
            $this->amsterdamLatitude,
            $this->amsterdamLongitude
        );
    }

}
