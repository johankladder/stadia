<?php


namespace JohanKladder\Stadia\Tests\Models;

use JohanKladder\Stadia\Models\StadiaLevelDuration;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaLevelDurationUnitTest extends TestCase
{

    public function test_get_ranges()
    {
        $duration = StadiaLevelDuration::create([
            'duration' => 5
        ]);
        $this->assertEquals(5, $duration->duration);
    }

    public function test_get_country_id_when_none()
    {
        $duration = StadiaLevelDuration::create([
            'duration' => 5
        ]);
        $this->assertNull($duration->country_id);
    }

    public function test_get_country_id()
    {
        $duration = StadiaLevelDuration::create([
            'duration' => 5,
            'country_id' => 1
        ]);
        $this->assertEquals(1, $duration->country_id);
    }

    public function test_get_climate_code_id()
    {
        $duration = StadiaLevelDuration::create([
            'duration' => 5,
            'climate_code_id' => 1
        ]);
        $this->assertEquals(1, $duration->climate_code_id);
    }
}
