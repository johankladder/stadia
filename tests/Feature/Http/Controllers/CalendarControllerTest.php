<?php


namespace JohanKladder\Stadia\Tests\Feature\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;
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

    private function createPlant()
    {
        return StadiaPlant::create();
    }

    private function createCountry()
    {
        return Country::create();
    }

}
