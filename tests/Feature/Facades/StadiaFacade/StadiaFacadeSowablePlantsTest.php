<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;

use Illuminate\Database\Eloquent\Collection;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\Interfaces\StadiaRelatedPlant;
use JohanKladder\Stadia\Models\StadiaPlant;
use JohanKladder\Stadia\Models\StadiaPlantCalendarRange;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeSowablePlantsTest extends TestCase
{

    /** @test */
    public function get_sowable_plants_when_empty_references()
    {
        $items = Stadia::getSowable(Collection::make(), now());
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_sowable_plants_when_references_but_no_stadia_plants()
    {
        $items = Stadia::getSowable(Collection::make([
            $this->createMock(StadiaRelatedPlant::class)
        ]), now());
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_sowable_plants_when_references_and_stadia_plant_but_no_ranges()
    {
        $this->createStadiaPlant(1);

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowable(Collection::make([
            $mock
        ]), now());
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_sowable_plants_when_references_and_stadia_plant_and_ranges_in_range()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $tomorrow = now()->addDay();
        $this->createCalendarRange($stadiaPlant, now()->subDay(), $tomorrow);

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowable(Collection::make([
            $mock
        ]), now());
        $this->assertCount(1, $items);
        $this->assertEquals(now()->addDay()->setHour(0)->roundHour(), $items[0]->sowable_till);
    }

    /** @test */
    public function get_sowable_plants_when_references_and_stadia_plant_and_ranges_in_range_other_year()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $tomorrow = now()->addDay();
        $this->createCalendarRange($stadiaPlant, now()->subDay()->addYear(), $tomorrow->addYear());

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowable(Collection::make([
            $mock
        ]), now());
        $this->assertCount(1, $items);
        $this->assertEquals(now()->addDay()->setHour(0)->roundDay(), $items[0]->sowable_till);
    }

    /** @test */
    public function get_sowable_plants_when_references_and_stadia_plant_and_ranges_in_range_multiple()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $tomorrow = now()->addDay();
        $this->createCalendarRange($stadiaPlant, now()->subDay(), $tomorrow);
        $this->createCalendarRange($stadiaPlant, now()->addMonth(), now()->addMonths(2));

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowable(Collection::make([
            $mock
        ]), now());
        $this->assertCount(1, $items);
        $this->assertEquals($tomorrow->setHour(0)->roundDay(), $items[0]->sowable_till);
    }

    /** @test */
    public function get_sowable_plants_when_references_and_stadia_plant_and_ranges_not_in_range()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $this->createCalendarRange($stadiaPlant, now()->addDay(), now()->addDays(2));

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowable(Collection::make([
            $mock
        ]), now());
        $this->assertCount(0, $items);
    }

    /** @test */
    public function get_sowable_plants_when_multiple_references_and_stadia_plants_and_ranges_in_range()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $stadiaPlant2 = $this->createStadiaPlant(2);
        $this->createCalendarRange($stadiaPlant, now()->subDay(), now()->addDay());
        $this->createCalendarRange($stadiaPlant2, now()->subDay(), now()->addDay());

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock2 = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");
        $mock2->method('getId')->willReturn(2);
        $mock2->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowable(Collection::make([
            $mock,
            $mock2
        ]), now());
        $this->assertCount(2, $items);
    }

    /** @test */
    public function get_sowable_plants_when_multiple_references_and_stadia_plants_and_ranges_in_range_and_not()
    {
        $stadiaPlant = $this->createStadiaPlant(1);
        $stadiaPlant2 = $this->createStadiaPlant(2);
        $this->createCalendarRange($stadiaPlant, now()->subDay(), now()->addDay());
        $this->createCalendarRange($stadiaPlant2, now()->addDay(), now()->addDays(2));

        $mock = $this->createMock(StadiaRelatedPlant::class);
        $mock2 = $this->createMock(StadiaRelatedPlant::class);
        $mock->method('getId')->willReturn(1);
        $mock->method('getTableName')->willReturn("plants");
        $mock2->method('getId')->willReturn(2);
        $mock2->method('getTableName')->willReturn("plants");

        $items = Stadia::getSowable(Collection::make([
            $mock,
            $mock2
        ]), now());
        $this->assertCount(1, $items);
        $this->assertEquals(1, $items[0]->getId());
    }

    private function createStadiaPlant($refId = 1)
    {
        return StadiaPlant::create([
            'reference_id' => $refId,
            'reference_table' => 'plants'
        ]);
    }

    private function createCalendarRange($stadiaPlant, $rangeFrom = null, $rangeTo = null, $country = null, $climate = null)
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
