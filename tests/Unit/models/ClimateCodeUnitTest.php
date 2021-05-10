<?php


namespace JohanKladder\Stadia\Tests\Models;

use JohanKladder\Stadia\Models\ClimateCode;
use JohanKladder\Stadia\Tests\TestCase;

class ClimateCodeUnitTest extends TestCase
{

    public function test_get_id()
    {
        $item = ClimateCode::create([
            'code' => 'Af'
        ]);
        $this->assertNotNull($item->id);
    }

    public function test_get_code()
    {
        $item = ClimateCode::create([
            'code' => 'Af'
        ]);
        $this->assertEquals('Af', $item->code);
    }

}
