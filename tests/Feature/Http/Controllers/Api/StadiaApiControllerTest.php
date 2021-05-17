<?php


namespace JohanKladder\Stadia\Tests\Feature\Http\Controllers\Api;


use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaApiControllerTest extends TestCase
{

    /** @test */
    public function get_all_calendar_ranges_successful_empty()
    {
        $response = $this->get("/stadia/api/calendar");
        $response->assertOk();
        $result = $response->json()['data'];
        $this->assertEmpty($result);
    }

    /** @test */
    public function get_all_calendar_ranges_successful_filled()
    {
        $stadiaPlant = $this->createPlant();
        $calendarRange = $this->createCalendarRanges($stadiaPlant);
        $response = $this->get("/stadia/api/calendar");
        $response->assertOk();
        $result = $response->json()['data'];
        $this->assertCount(1, $result);
        $this->assertEquals($stadiaPlant->id, $result[0]["id"]);
        $this->assertCount(1, $result[0]["ranges"]);
        $this->assertEquals($calendarRange->id, $result[0]["ranges"][0]['id']);
        $this->checkRangeContent($result[0]["ranges"][0]);
    }

    /** @test */
    public function get_all_calendar_ranges_successful_with_country_empty()
    {
        $country = $this->createCountry();
        $response = $this->get("/stadia/api/calendar/" . $country->id);
        $response->assertOk();
        $result = $response->json()['data'];
        $this->assertEmpty($result);
    }

    /** @test */
    public function get_all_calendar_ranges_successful_with_country_filled()
    {
        $stadiaPlant = $this->createPlant();
        $country = $this->createCountry();
        $calendarRange = $this->createCalendarRanges($stadiaPlant, $country);
        $response = $this->get("/stadia/api/calendar/" . $country->id);
        $response->assertOk();
        $result = $response->json()['data'];

        $this->assertCount(1, $result);
        $this->assertEquals($stadiaPlant->id, $result[0]["id"]);
        $this->assertCount(1, $result[0]["ranges"]);
        $this->assertEquals($calendarRange->id, $result[0]["ranges"][0]['id']);

        $this->checkRangeContent($result[0]["ranges"][0]);
    }

    /** @test */
    public function get_all_calendar_ranges_successful_with_country_and_climate_code_empty()
    {
        $country = $this->createCountry();
        $climateCode = $this->createClimateCode();
        $response = $this->get("/stadia/api/calendar/" . $country->id . "/" . $climateCode->id);
        $response->assertOk();
        $result = $response->json()['data'];
        $this->assertEmpty($result);
    }

    /** @test */
    public function get_all_calendar_ranges_successful_with_country_and_climate_code_filled()
    {
        $stadiaPlant = $this->createPlant();
        $country = $this->createCountry();
        $climateCode = $this->createClimateCode();
        $calendarRange = $this->createCalendarRanges($stadiaPlant, $country, $climateCode);
        $response = $this->get("/stadia/api/calendar/" . $country->id . "/" . $climateCode->id);
        $response->assertOk();
        $result = $response->json()['data'];

        $this->assertCount(1, $result);
        $this->assertEquals($stadiaPlant->id, $result[0]["id"]);
        $this->assertCount(1, $result[0]["ranges"]);
        $this->assertEquals($calendarRange->id, $result[0]["ranges"][0]['id']);
        $this->checkRangeContent($result[0]["ranges"][0]);
    }

    /** @test */
    public function get_calendar_ranges_plant_successful_empty()
    {
        $stadiaPlant = $this->createPlant();
        $response = $this->get("/stadia/api/calendar-plant/" . $stadiaPlant->id);
        $response->assertOk();
        $result = $response->json()['data'];
        $this->assertEmpty($result['ranges']);
    }


    /** @test */
    public function get_calendar_ranges_successful_filled()
    {
        $stadiaPlant = $this->createPlant();
        $this->createCalendarRanges($stadiaPlant);
        $response = $this->get("/stadia/api/calendar-plant/" . $stadiaPlant->id);
        $response->assertOk();
        $result = $response->json()['data'];
        $this->assertEquals($stadiaPlant->id, $result["id"]);
        $this->assertCount(1, $result["ranges"]);
        $this->checkRangeContent($result['ranges'][0]);
    }

    /** @test */
    public function get_calendar_ranges_incorrect_plant()
    {
        $response = $this->get("/stadia/api/calendar-plant/1223333");
        $response->assertNotFound();
    }

    private function checkRangeContent($range, $contentKeys = ['id', 'range_from', 'range_to'])
    {
        foreach ($contentKeys as $contentKey) {
            $this->assertArrayHasKey($contentKey, $range);
        }
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

    private function createCalendarRanges(StadiaPlant $stadiaPlant, Country $country = null, ClimateCode $climateCode = null)
    {
        return StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'range_from' => now(),
            'range_to' => now(),
            'country_id' => $country ? $country->id : null,
            'climate_code_id' => $climateCode ? $climateCode->id : null
        ]);
    }

}
