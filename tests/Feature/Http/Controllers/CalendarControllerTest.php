<?php


namespace JohanKladder\Stadia\Tests\Feature\Http\Controllers;


use Illuminate\Database\Eloquent\Collection;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;
use JohanKladder\Stadia\Tests\TestCase;

class CalendarControllerTest extends TestCase
{

    /** @test */
    public function store_calendar_ranges_happy_flow()
    {
        $stadiaPlant = $this->createPlant();
        $from = now();
        $to = now();
        $response = $this->post(route('calendar.store', $stadiaPlant->id), [
            'range_from' => $from,
            'range_to' => $to,
        ]);

        $this->assertDatabaseHas('stadia_plant_calendar_ranges', [
            'range_from' => $from,
            'range_to' => $to,
            'country_id' => null,
            'stadia_plant_id' => $stadiaPlant->id
        ]);
        $response->assertStatus(302);
    }

    /** @test */
    public function store_calendar_ranges_happy_flow_with_country()
    {
        $stadiaPlant = $this->createPlant();
        $country = $this->createCountry();
        $from = now();
        $to = now();
        $response = $this->post(route('calendar.store', $stadiaPlant->id), [
            'range_from' => $from,
            'range_to' => $to,
            'country_id' => $country->id
        ]);

        $this->assertDatabaseHas('stadia_plant_calendar_ranges', [
            'range_from' => $from,
            'range_to' => $to,
            'country_id' => $country->id,
            'stadia_plant_id' => $stadiaPlant->id
        ]);
        $response->assertStatus(302);
    }

    /** @test */
    public function store_calendar_ranges_happy_flow_with_climate_code_and_country()
    {
        $stadiaPlant = $this->createPlant();
        $country = $this->createCountry();
        $climateCode = $this->createClimateCode();
        $from = now();
        $to = now();
        $response = $this->post(route('calendar.store', $stadiaPlant->id), [
            'range_from' => $from,
            'range_to' => $to,
            'country_id' => $country->id,
            'climate_code_id' => $climateCode->id
        ]);

        $this->assertDatabaseHas('stadia_plant_calendar_ranges', [
            'range_from' => $from,
            'range_to' => $to,
            'country_id' => $country->id,
            'stadia_plant_id' => $stadiaPlant->id,
            'climate_code_id' => $climateCode->id
        ]);
        $response->assertStatus(302);
    }

    /** @test */
    public function store_calendar_ranges_with_missing_plant()
    {
        $response = $this->post("/stadia/calendar/");
        $response->assertStatus(404);
    }

    /** @test */
    public function store_calendar_ranges_with_not_existing_plant()
    {
        $response = $this->post("/stadia/calendar/123");
        $response->assertStatus(404);
    }

    /** @test */
    public function store_calendar_ranges_with_wrong_parameters()
    {
        $stadiaPlant = $this->createPlant();
        $response = $this->post(route('calendar.store', $stadiaPlant->id), [
            'range_from' => null,
            'range_to' => null,
        ]);

        $this->assertDatabaseMissing('stadia_plant_calendar_ranges', [
            'range_from' => null,
            'range_to' => null,
            'stadia_plant_id' => $stadiaPlant->id
        ]);

        $response->assertSessionHasErrors(['range_from', 'range_to']);
    }

    /** @test */
    public function store_calendar_ranges_with_not_existing_country()
    {
        $stadiaPlant = $this->createPlant();
        $response = $this->post(route('calendar.store', $stadiaPlant->id), [
            'range_from' => now(),
            'range_to' => now(),
            'country_id' => 123
        ]);

        $this->assertDatabaseMissing('stadia_plant_calendar_ranges', [
            'stadia_plant_id' => $stadiaPlant->id,
        ]);
        $response->assertSessionHasErrors(['country_id']);
    }

    /** @test */
    public function get_calendar_ranges_when_none()
    {
        $response = $this->get("stadia/calendar/" . $this->createPlant()->id);
        $response->assertOk();
        $response->assertViewHas("itemsGlobal", Collection::make([]));
        $response->assertViewHas("itemsCountry", Collection::make([]));
    }

    /** @test */
    public function get_calendar_ranges_when_only_globally()
    {
        $stadiaPlant = $this->createPlant();
        StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'range_from' => now(),
            'range_to' => now(),
        ]);
        $response = $this->get("stadia/calendar/" . $stadiaPlant->id);
        $response->assertOk();

        $itemsGlobal = $response->viewData('itemsGlobal');
        $itemsCountry = $response->viewData('itemsCountry');
        $this->assertCount(1, $itemsGlobal);
        $this->assertCount(0, $itemsCountry);
    }

    /** @test */
    public function get_calendar_ranges_when_globally_and_country()
    {
        $stadiaPlant = $this->createPlant();
        StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'range_from' => now(),
            'range_to' => now(),
        ]);

        StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'range_from' => now(),
            'range_to' => now(),
            'country_id' => $this->createCountry()->id
        ]);
        $response = $this->get("stadia/calendar/" . $stadiaPlant->id);
        $response->assertOk();

        $itemsGlobal = $response->viewData('itemsGlobal');
        $itemsCountry = $response->viewData('itemsCountry');
        $this->assertCount(1, $itemsGlobal);
        $this->assertCount(1, $itemsCountry);

    }

    /** @test */
    public function get_calendar_ranges_with_country_when_none()
    {
        $response = $this->get("stadia/calendar/" . $this->createPlant()->id . "?country=" . $this->createCountry()->id);
        $response->assertOk();
        $response->assertViewHas("itemsGlobal", Collection::make([]));
        $response->assertViewHas("itemsCountry", Collection::make([]));
    }

    /** @test */
    public function get_calendar_ranges_with_country_when_only_globally()
    {
        $stadiaPlant = $this->createPlant();
        StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'range_from' => now(),
            'range_to' => now()
        ]);
        $response = $this->get("stadia/calendar/" . $stadiaPlant->id . "?country=" . $this->createCountry()->id);
        $response->assertOk();
        $itemsCountry = $response->viewData('selectedCalendar');
        $this->assertCount(1, $itemsCountry);
    }

    /** @test */
    public function get_calendar_ranges_with_country_when_defined()
    {
        $stadiaPlant = $this->createPlant();
        $country = $this->createCountry();

        $incorrect = StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'range_from' => now(),
            'range_to' => now(),
        ]);

        $correct = StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'range_from' => now(),
            'range_to' => now(),
            'country_id' => $country->id
        ]);
        $response = $this->get("stadia/calendar/" . $stadiaPlant->id . "?country=" . $country->id);
        $response->assertOk();
        $itemsCountry = $response->viewData('selectedCalendar');
        $this->assertCount(1, $itemsCountry);
        $this->assertEquals($correct->id, $itemsCountry[0]->id);
        $this->assertNotEquals($incorrect->id, $itemsCountry[0]->id);
    }

    private function createPlant()
    {
        return StadiaPlant::create();
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
