<?php


namespace JohanKladder\Stadia\Tests\Feature\Http\Controllers;


use Illuminate\Database\Eloquent\Collection;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaLevelDuration;
use JohanKladder\Stadia\Models\StadiaPlant;
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

    /** @test */
    public function get_durations_when_none()
    {
        $response = $this->get(route("durations.index", $this->createStadiaLevel()));
        $response->assertOk();
        $response->assertViewHas("itemsGlobal", Collection::make([]));
        $response->assertViewHas("itemsCountry", Collection::make([]));
        $response->assertViewHas("itemsClimateCode", Collection::make([]));
    }

    /** @test */
    public function get_durations_when_only_globally()
    {
        $stadiaLevel = $this->createStadiaLevel();
        StadiaLevelDuration::create([
            'stadia_level_id' => $stadiaLevel->id,
            'duration' => 5
        ]);

        $response = $this->get(route("durations.index", $stadiaLevel->id));
        $response->assertOk();
        $itemsGlobal = $response->viewData('itemsGlobal');
        $itemsCountry = $response->viewData('itemsCountry');
        $this->assertCount(1, $itemsGlobal);
        $this->assertCount(0, $itemsCountry);
        $response->assertViewHas("itemsClimateCode", Collection::make([]));
    }


    /** @test */
    public function get_durations_when_globally_and_country()
    {
        $stadiaLevel = $this->createStadiaLevel();
        StadiaLevelDuration::create([
            'stadia_level_id' => $stadiaLevel->id,
            'duration' => 5
        ]);

        StadiaLevelDuration::create([
            'stadia_level_id' => $stadiaLevel->id,
            'duration' => 5,
            'country_id' => $this->createCountry()->id
        ]);

        $response = $this->get(route("durations.index", $stadiaLevel->id));
        $response->assertOk();
        $itemsGlobal = $response->viewData('itemsGlobal');
        $itemsCountry = $response->viewData('itemsCountry');
        $this->assertCount(1, $itemsGlobal);
        $this->assertCount(1, $itemsCountry);

    }

    /** @test */
    public function get_durations_when_climate_code()
    {
        $stadiaLevel = $this->createStadiaLevel();

        StadiaLevelDuration::create([
            'stadia_level_id' => $stadiaLevel->id,
            'duration' => 5,
            'country_id' => $this->createCountry()->id,
            'climate_code_id' => $this->createClimateCode()->id,
        ]);

        $response = $this->get(route("durations.index", $stadiaLevel->id));
        $response->assertOk();

        $itemsGlobal = $response->viewData('itemsGlobal');
        $itemsCountry = $response->viewData('itemsCountry');
        $itemsClimateCode = $response->viewData('itemsClimateCode');
        $this->assertCount(0, $itemsGlobal);
        $this->assertCount(0, $itemsCountry);
        $this->assertCount(1, $itemsClimateCode);

    }

    /** @test */
    public function get_durations_with_country_when_none()
    {
        $response = $this->get("stadia/durations/" . $this->createStadiaLevel()->id . "?country=" . $this->createCountry()->id);
        $response->assertOk();
        $itemsCountry = $response->viewData('selectedDurations');
        $this->assertCount(0, $itemsCountry);
    }

    /** @test */
    public function get_durations_with_country_when_only_globally()
    {
        $stadiaLevel = $this->createStadiaLevel();

        StadiaLevelDuration::create([
            'stadia_level_id' => $stadiaLevel->id,
            'duration' => 5,
        ]);

        $response = $this->get("stadia/durations/" . $stadiaLevel->id . "?country=" . $this->createCountry()->id);
        $response->assertOk();
        $itemsCountry = $response->viewData('selectedDurations');
        $this->assertCount(1, $itemsCountry);
    }

    /** @test */
    public function get_durations_with_country_when_defined()
    {
        $stadiaLevel = $this->createStadiaLevel();
        $country = $this->createCountry();

        $incorrect = StadiaLevelDuration::create([
            'stadia_level_id' => $stadiaLevel->id,
            'duration' => 5
        ]);

        $correct = StadiaLevelDuration::create([
            'stadia_level_id' => $stadiaLevel->id,
            'duration' => 5,
            'country_id' => $country->id
        ]);
        $response = $this->get("stadia/durations/" . $stadiaLevel->id . "?country=" . $country->id);
        $response->assertOk();
        $itemsCountry = $response->viewData('selectedDurations');
        $this->assertCount(1, $itemsCountry);
        $this->assertEquals($correct->id, $itemsCountry[0]->id);
        $this->assertNotEquals($incorrect->id, $itemsCountry[0]->id);
    }

    private function createStadiaLevel()
    {
        return StadiaLevel::create([
            'name' => 'Level',
            'stadia_plant_id' => StadiaPlant::create()->id
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
