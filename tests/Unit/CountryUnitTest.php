<?php


namespace JohanKladder\Stadia\Tests;

use JohanKladder\Stadia\Models\Country;

class CountryUnitTest extends TestCase
{

    public function test_get_id()
    {
        $item = Country::create([]);
        $this->assertNotNull($item->id);
    }

    public function test_get_name() {
        $item = Country::create([
            'name' => 'Netherlands'
        ]);
        $this->assertEquals('Netherlands', $item->name);
    }

    public function test_get_code() {
        $item = Country::create([
            'code' => 'NL'
        ]);
        $this->assertEquals('NL', $item->code);
    }

}
