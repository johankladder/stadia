<?php


namespace JohanKladder\Stadia\Tests\Unit\models\Information;


use JohanKladder\Stadia\Models\Information\KoepenLocation;
use JohanKladder\Stadia\Tests\TestCase;

class KoepenLocationUnitTest extends TestCase
{

    /** @test */
    public function get_location_values()
    {
        $koepenLocation = KoepenLocation::create([
            'latitude' => -89.75,
            'longitude' => -179.75,
            'code' => "EF"
        ]);

        $this->assertEquals(-89.75, $koepenLocation->latitude);
        $this->assertEquals(-179.75, $koepenLocation->longitude);
        $this->assertEquals('EF', $koepenLocation->code);
    }
}
