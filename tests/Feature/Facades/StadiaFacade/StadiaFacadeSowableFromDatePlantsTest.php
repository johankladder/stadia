<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;

use Illuminate\Database\Eloquent\Collection;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\Interfaces\StadiaRelatedPlant;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeSowableFromDatePlantsTest extends TestCase
{

    /** @test */
    public function get_sowable_from_dates_plants_when_empty_references()
    {
        $items = Stadia::getSowableFromDate(Collection::make(), now());
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_sowable_from_dates_when_references_but_no_stadia_plants()
    {
        $items = Stadia::getSowableFromDate(Collection::make([
            $this->createMock(StadiaRelatedPlant::class)
        ]), now());
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_sowable_from_dates_when_references_and_stadia_plant_but_no_ranges()
    {
        $this->createStadiaPlant(1);

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowableFromDate(Collection::make([
            $mock
        ]), now());
        $this->assertCount(0, $items);
    }


    /** @test */
    public function get_sowable_from_dates_when_references_and_stadia_plant_and_ranges_in_range()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $tomorrow = now()->addDay();
        $this->createCalendarRange($stadiaPlant, now()->subDay(), $tomorrow);

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowableFromDate(Collection::make([
            $mock
        ]), now());
        $this->assertCount(1, $items);
        $this->assertEquals(now()->subDay()->setHour(0)->roundDay(), $items[0]->sowable_from);
    }


    /** @test */
    public function get_sowable_from_dates_when_references_and_stadia_plant_and_ranges_out_range()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $this->createCalendarRange($stadiaPlant, now()->addWeek(), now()->addWeeks(2));

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowableFromDate(Collection::make([
            $mock
        ]), now());
        $this->assertCount(1, $items);
        $this->assertEquals(now()->addWeek()->setHour(0)->roundDay(), $items[0]->sowable_from);
    }

    /** @test */
    public function get_sowable_from_dates_when_references_and_stadia_plant_and_ranges_before_date()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $this->createCalendarRange($stadiaPlant, now()->subWeeks(2), now()->subWeek());

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowableFromDate(Collection::make([
            $mock
        ]), now());
        $this->assertCount(1, $items);
        $this->assertEquals(now()->subWeeks(2)->setHour(0)->roundDay(), $items[0]->sowable_from);
    }


    /** @test */
    public function get_sowable_from_dates_when_references_and_stadia_plant_and_ranges_before_and_after_date()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $this->createCalendarRange($stadiaPlant, now()->subWeeks(2), now()->subWeek());
        $this->createCalendarRange($stadiaPlant, now()->addWeek(), now()->addWeeks(2));

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowableFromDate(Collection::make([
            $mock
        ]), now());
        $this->assertCount(1, $items);
        $this->assertEquals(now()->addWeek()->setHour(0)->roundDay(), $items[0]->sowable_from);
    }


    private
    function createStadiaPlant($refId = 1)
    {
        return StadiaPlant::create([
            'reference_id' => $refId,
            'reference_table' => 'plants'
        ]);
    }

    private
    function createCalendarRange($stadiaPlant, $rangeFrom = null, $rangeTo = null, $country = null, $climate = null)
    {
        return StadiaPlantCalendarRange::create([
            'stadia_plant_id' => $stadiaPlant->id,
            'country_id' => $country ? $country->id : null,
            'climate_code_id' => $climate ? $climate->id : null,
            'range_from' => $rangeFrom,
            'range_to' => $rangeTo
        ]);
    }


}
