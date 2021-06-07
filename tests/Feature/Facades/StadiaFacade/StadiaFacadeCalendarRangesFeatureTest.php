<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;

use Illuminate\Support\Collection;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeCalendarRangesFeatureTest extends TestCase
{

    /** @test */
    public function get_calendar_ranges_of_stadia_plant_when_none()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $items = Stadia::getCalendarRanges($stadiaPlant);
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_calendar_ranges_of_stadia_plant_when_global()
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
        $this->createCalendarRange($stadiaPlant); // Create global range
        $calendarRangeCountry = $this->createCalendarRange($stadiaPlant, $country);
        $items = Stadia::getCalendarRanges($stadiaPlant, $country);
        $this->assertCount(1, $items);
        $this->assertEquals($calendarRangeCountry->id, $items[0]->id);
    }


    /** @test */
    public function get_calendar_ranges_of_reference_when_none()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $items = Stadia::getCalendarRangesWithReference($stadiaPlant->reference_id);
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_calendar_ranges_of_stadia_plant_when_country_and_climate()
    {
        $country = $this->createCountry();
        $climate = $this->createClimateCode();
        $stadiaPlant = $this->createStadiaPlant();
        $this->createCalendarRange($stadiaPlant, $country);
        $calendarRangeClimate = $this->createCalendarRange($stadiaPlant, $country, $climate);
        $items = Stadia::getCalendarRanges($stadiaPlant, $country, $climate);
        $this->assertCount(1, $items);
        $this->assertEquals($calendarRangeClimate->id, $items[0]->id);
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
        $items = Stadia::getCalendarRangesOf(Collection::make([$stadiaPlant, $stadiaPlantOther]));
        $this->assertCount(2, $items);

        $this->assertCount(1, $items[0]["ranges"]);
        $this->assertCount(1, $items[1]["ranges"]);

        $this->assertEquals($calendarRange->id, $items[0]["ranges"][0]->id);
        $this->assertEquals($calendarRangeOther->id, $items[1]["ranges"][0]->id);
    }

    /** @test */
    public function get_calendar_ranges_of_all_stadia_plants()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $stadiaPlantOther = $this->createStadiaPlant(2);

        $calendarRange = $this->createCalendarRange($stadiaPlant);
        $calendarRangeOther = $this->createCalendarRange($stadiaPlantOther);

        $items = Stadia::getCalendarRangesOfAllPlants();

        $this->assertCount(2, $items);
        $this->assertCount(1, $items[0]["ranges"]);
        $this->assertCount(1, $items[1]["ranges"]);

        $this->assertEquals($stadiaPlant->id, $items[0]["reference_id"]);
        $this->assertEquals($stadiaPlantOther->id, $items[1]["reference_id"]);

        $this->assertEquals($calendarRange->id, $items[0]["ranges"][0]->id);
        $this->assertEquals($calendarRangeOther->id, $items[1]["ranges"][0]->id);
    }


    private function createStadiaPlant($refId = 1)
    {
        return StadiaPlant::create([
            'reference_id' => $refId,
            'reference_table' => 'plants'
        ]);
    }

    private function createCountry()
    {
        return Country::create();
    }

    private function createClimateCode()
    {
        return ClimateCode::create([
            'code' => 'test'
        ]);
    }

    private function createCalendarRange($stadiaPlant, $country = null, $climate = null)
    {
        return StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'country_id' => $country ? $country->id : null,
            'climate_code_id' => $climate ? $climate->id : null,
        ]);
    }


}
