<?php

namespace JohanKladder\Stadia\Tests\Feature;

use Illuminate\Support\Collection;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeFeatureTest extends TestCase
{


    /** @test */
    public function gather_all_stadia_plants_when_none()
    {
        $items = Stadia::getAllPlants();
        $this->assertCount(0, $items);
    }

    /** @test */
    public function gather_all_stadia_plants()
    {
        $this->createStadiaPlant();
        $items = Stadia::getAllPlants();
        $this->assertCount(1, $items);
    }

    /** @test */
    public function gather_all_countries_when_none()
    {
        $items = Stadia::getAllCountries();
        $this->assertCount(0, $items);
    }

    /** @test */
    public function gather_all_countries()
    {
        $this->createCountry();
        $items = Stadia::getAllCountries();
        $this->assertCount(1, $items);
    }

    /** @test */
    public function get_calendar_ranges_of_stadia_plant_when_none()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $items = Stadia::getCalendarRanges($stadiaPlant);
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_calendar_ranges_of_stadia_plant_when_globally_set()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $calendarRange = $this->createCalendarRange($stadiaPlant);
        $items = Stadia::getCalendarRanges($stadiaPlant);
        $this->assertCount(1, $items);
        $this->assertEquals($calendarRange->id, $items[0]->id);
    }

    /** @test */
    public function get_calendar_ranges_of_stadia_plant_when_globally_and_country()
    {
        $country = $this->createCountry();
        $stadiaPlant = $this->createStadiaPlant();
        $this->createCalendarRange($stadiaPlant);
        $calendarRangeCountry = $this->createCalendarRange($stadiaPlant, $country);
        $items = Stadia::getCalendarRanges($stadiaPlant, $country);
        $this->assertCount(1, $items);
        $this->assertEquals($calendarRangeCountry->id, $items[0]->id);
    }

    /** @test */
    public function get_calendar_ranges_of_stadia_plant_when_only_country()
    {
        $country = $this->createCountry();
        $stadiaPlant = $this->createStadiaPlant();
        $calendarRangeCountry = $this->createCalendarRange($stadiaPlant, $country);
        $items = Stadia::getCalendarRanges($stadiaPlant, $country);
        $this->assertCount(1, $items);
        $this->assertEquals($calendarRangeCountry->id, $items[0]->id);
    }

    /** @test */
    public function get_calendar_ranges_of_stadia_plant_when_globally_and_country_not_set()
    {
        $country = $this->createCountry();
        $stadiaPlant = $this->createStadiaPlant();
        $calendarRange = $this->createCalendarRange($stadiaPlant);
        $this->createCalendarRange($stadiaPlant, $this->createCountry());
        $items = Stadia::getCalendarRanges($stadiaPlant, $country);
        $this->assertCount(1, $items);
        $this->assertEquals($calendarRange->id, $items[0]->id);
    }

    /** @test */
    public function get_calendar_ranges_of_multiple_stadia_plants()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaPlantOther = $this->createStadiaPlant();
        $calendarRange = $this->createCalendarRange($stadiaPlant);
        $calendarRangeOther = $this->createCalendarRange($stadiaPlantOther);
        $items = Stadia::getCalendarRangesOfStadiaPlants(Collection::make([$stadiaPlant, $stadiaPlantOther]));
        $this->assertCount(2, $items);
        $this->assertCount(1, $items[0]);
        $this->assertCount(1, $items[1]);
        $this->assertEquals($calendarRange->id, $items[0][0]->id);
        $this->assertEquals($calendarRangeOther->id, $items[1][0]->id);
    }

    /** @test */
    public function get_calendar_ranges_of_all_stadia_plants()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaPlantOther = $this->createStadiaPlant();
        $calendarRange = $this->createCalendarRange($stadiaPlant);
        $calendarRangeOther = $this->createCalendarRange($stadiaPlantOther);
        $items = Stadia::getCalendarRangesOfAllStadiaPlants();
        $this->assertCount(2, $items);
        $this->assertCount(1, $items[0]);
        $this->assertCount(1, $items[1]);
        $this->assertEquals($calendarRange->id, $items[0][0]->id);
        $this->assertEquals($calendarRangeOther->id, $items[1][0]->id);
    }

    private function createStadiaPlant()
    {
        return StadiaPlant::create();
    }

    private function createCountry()
    {
        return Country::create();
    }

    private function createCalendarRange($stadiaPlant, $country = null)
    {
        return StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'country_id' => $country ? $country->id : null
        ]);
    }


}
