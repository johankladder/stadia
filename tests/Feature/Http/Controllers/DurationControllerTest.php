<?php


namespace JohanKladder\Stadia\Tests\Feature\Http\Controllers;


use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Tests\TestCase;

class DurationControllerTest extends TestCase
{

    /** @test */
    public function store_duration_happy_flow()
    {
        $stadiaLevel = $this->createStadiaLevel();
        $response = $this->post(route('durations.store', $stadiaLevel->id), [
            'duration' => 5,
        ]);

        $this->assertDatabaseHas('stadia_level_durations', [
            'duration' => 5,
            'stadia_level_id' => $stadiaLevel->id
        ]);
        $response->assertStatus(302);
    }

    /** @test */
    public function store_duration_with_country()
    {
        $stadiaLevel = $this->createStadiaLevel();
        $country = $this->createCountry();

        $response = $this->post(route('durations.store', $stadiaLevel->id), [
            'duration' => 5,
            'country_id' => $country->id
        ]);

        $this->assertDatabaseHas('stadia_level_durations', [
            'duration' => 5,
            'stadia_level_id' => $stadiaLevel->id,
            'country_id' => $country->id
        ]);
        $response->assertStatus(302);
    }

    /** @test */
    public function store_duration_with_country_and_climate_code()
    {
        $stadiaLevel = $this->createStadiaLevel();
        $country = $this->createCountry();
        $climateCode = $this->createClimateCode();

        $response = $this->post(route('durations.store', $stadiaLevel->id), [
            'duration' => 5,
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);

        $this->assertDatabaseHas('stadia_level_durations', [
            'duration' => 5,
            'stadia_level_id' => $stadiaLevel->id,
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);
        $response->assertStatus(302);
    }

    /** @test */
    public function store_duration_missing_duration()
    {
        $stadiaLevel = $this->createStadiaLevel();
        $response = $this->post(route('durations.store', $stadiaLevel->id), [

        ]);
        $this->assertDatabaseMissing('stadia_level_durations', [
            'stadia_level_id' => $stadiaLevel->id
        ]);

        $response->assertSessionHasErrors(['duration']);
    }

    /** @test */
    public function store_duration_wrong_stadia_level()
    {
        $response = $this->post(route('durations.store', 99999));
        $response->assertNotFound();
    }

    /** @test */
    public function store_duration_with_climate_code_missing_country_code()
    {
        $stadiaLevel = $this->createStadiaLevel();
        $climateCode = $this->createClimateCode();

        $response = $this->post(route('durations.store', $stadiaLevel->id), [
            'duration' => 5,
            'country_id' => null,
            'climate_code_id' => $climateCode->id
        ]);

        $response->assertSessionHasErrors(['country_id']);

        $this->assertDatabaseMissing('stadia_level_durations', [
            'stadia_level_id' => $stadiaLevel->id
        ]);

    }

    private function createStadiaLevel()
    {
        return StadiaLevel::create([
            'name' => 'Level'
        ]);
    }


    private function createCountry()
    {
        return Country::create();
    }

    private function createClimateCode()
    {
        return ClimateCode::create([
            'code' => 'Code'
        ]);
    }

}
