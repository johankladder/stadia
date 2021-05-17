<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades;

use Illuminate\Support\Collection;
use JohanKladder\Stadia\Exceptions\NoDurationsException;
use JohanKladder\Stadia\Exceptions\NoStadiaLevelsException;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Models\StadiaLevelDuration;
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
        $items = Stadia::getCalendarRangesOf(Collection::make([$stadiaPlant, $stadiaPlantOther]));
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
        $items = Stadia::getCalendarRangesOfAllPlants();
        $this->assertCount(2, $items);
        $this->assertCount(1, $items[0]);
        $this->assertCount(1, $items[1]);
        $this->assertEquals($calendarRange->id, $items[0][0]->id);
        $this->assertEquals($calendarRangeOther->id, $items[1][0]->id);
    }

    /** @test */
    public function get_grow_time_no_levels()
    {
        $this->expectException(NoStadiaLevelsException::class);
        $stadiaPlant = $this->createStadiaPlant();
        Stadia::getGrowTime($stadiaPlant);
    }

    /** @test */
    public function get_grow_time_has_levels_no_durations()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $this->createStadiaLevel($stadiaPlant);
        $growTime = Stadia::getGrowTime($stadiaPlant);
        $this->assertEquals(0, $growTime);
    }

    /** @test */
    public function get_grow_time_has_levels_only_global()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $stadiaDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $growTime = Stadia::getGrowTime($stadiaPlant);
        $this->assertEquals($stadiaDuration->duration, $growTime);
    }

    /** @test */
    public function get_grow_time_has_levels_only_global_multiple_levels()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $stadiaLevel2 = $this->createStadiaLevel($stadiaPlant);
        $stadiaDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $this->createStadiaDuration($stadiaLevel2, 5);
        $growTime = Stadia::getGrowTime($stadiaPlant);
        $this->assertEquals($stadiaDuration->duration * 2, $growTime);
    }


    /** @test */
    public function get_grow_time_has_levels_global_and_country_based()
    {
        $country = $this->createCountry();
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $globalDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $countryDuration = $this->createStadiaDuration($stadiaLevel, 10, $country);
        $growTime = Stadia::getGrowTime($stadiaPlant, $country);
        $this->assertEquals($countryDuration->duration, $growTime);
        $this->assertNotEquals($globalDuration->duration, $growTime);
    }

    /** @test */
    public function get_grow_time_has_levels_country_and_climate_codes_based()
    {
        $country = $this->createCountry();
        $climate = $this->createClimateCode();
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $countryDuration = $this->createStadiaDuration($stadiaLevel, 5, $country);
        $climateDuration = $this->createStadiaDuration($stadiaLevel, 10, $country, $climate);
        $growTime = Stadia::getGrowTime($stadiaPlant, $country, $climate);
        $this->assertEquals($climateDuration->duration, $growTime);
        $this->assertNotEquals($countryDuration->duration, $growTime);
    }

    /** @test */
    public function get_duration_when_no_global_durations()
    {
        $this->expectException(NoDurationsException::class);
        $stadiaLevel = $this->createStadiaLevel();
        Stadia::getDuration($stadiaLevel);
    }

    /** @test */
    public function get_duration_when_global_durations()
    {
        $stadiaLevel = $this->createStadiaLevel();
        $levelDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $duration = Stadia::getDuration($stadiaLevel);
        $this->assertEquals($levelDuration->duration, $duration);
    }

    /** @test */
    public function get_duration_when_global_and_country_durations()
    {
        $country = $this->createCountry();
        $stadiaLevel = $this->createStadiaLevel();
        $globalDuration = $this->createStadiaDuration($stadiaLevel, 5);
        $countryDuration = $this->createStadiaDuration($stadiaLevel, 10, $country);
        $duration = Stadia::getDuration($stadiaLevel, $country);
        $this->assertEquals($countryDuration->duration, $duration);
        $this->assertNotEquals($globalDuration->duration, $duration);
    }

    /** @test */
    public function get_duration_when_country_and_climate_durations()
    {
        $country = $this->createCountry();
        $climate = $this->createClimateCode();
        $stadiaLevel = $this->createStadiaLevel();
        $countryDuration = $this->createStadiaDuration($stadiaLevel, 5, $country);
        $climateDuration = $this->createStadiaDuration($stadiaLevel, 10, $country, $climate);
        $duration = Stadia::getDuration($stadiaLevel, $country, $climate);
        $this->assertEquals($climateDuration->duration, $duration);
        $this->assertNotEquals($countryDuration->duration, $duration);
    }

    /** @test */
    public function get_durations_when_no_levels()
    {
        $this->expectException(NoStadiaLevelsException::class);
        $stadiaPlant = $this->createStadiaPlant();
        Stadia::getDurations($stadiaPlant);
    }

    /** @test */
    public function get_durations_happy_flow()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $duration = $this->createStadiaDuration($stadiaLevel, 5);
        $durations = Stadia::getDurations($stadiaPlant);
        $this->assertCount(1, $durations);
        $this->assertEquals($duration->duration, $durations[0]->duration);
    }

    /** @test */
    public function get_durations_happy_flow_multiple_levels()
    {
        $stadiaPlant = $this->createStadiaPlant();
        $stadiaLevel = $this->createStadiaLevel($stadiaPlant);
        $stadiaLevel2 = $this->createStadiaLevel($stadiaPlant);
        $duration = $this->createStadiaDuration($stadiaLevel, 5);
        $duration2 = $this->createStadiaDuration($stadiaLevel2, 5);
        $durations = Stadia::getDurations($stadiaPlant);
        $this->assertCount(2, $durations);
        $this->assertEquals($duration->duration, $durations[0]->duration);
        $this->assertEquals($duration2->duration, $durations[1]->duration);
    }

    private function createStadiaPlant()
    {
        return StadiaPlant::create();
    }

    private function createStadiaLevel(StadiaPlant $stadiaPlant = null)
    {
        return StadiaLevel::create([
            'name' => 'Level',
            'stadia_plant_id' => $stadiaPlant ? $stadiaPlant->id : $this->createStadiaPlant()->id
        ]);
    }

    private function createStadiaDuration(StadiaLevel $stadiaLevel, $duration, $country = null, $climate = null)
    {
        return StadiaLevelDuration::create([
            'stadia_level_id' => $stadiaLevel->id,
            'duration' => $duration,
            'country_id' => $country ? $country->id : null,
            'climate_code_id' => $climate ? $climate->id : null,
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

    private function createCalendarRange($stadiaPlant, $country = null)
    {
        return StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'country_id' => $country ? $country->id : null
        ]);
    }


}
