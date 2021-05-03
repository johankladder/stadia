<?php


namespace JohanKladder\Stadia\Tests\Unit\models;


use JohanKladder\Stadia\Models\StadiaLevel;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaLevelUnitTest extends TestCase
{

    /** @test */
    public function get_name_when_set()
    {
        $entity = StadiaLevel::create([
            'name' => "Test"
        ]);

        $this->assertEquals("Test", $entity->name);
        $this->assertNull($entity->reference_id);
        $this->assertNull($entity->reference_table);
    }

    /** @test */
    public function get_table_references_when_set()
    {
        $entity = StadiaLevel::create([
            'name' => "Test",
            'reference_id' => 1,
            'reference_table' => 'levels',
        ]);

        $this->assertEquals("Test", $entity->name);
        $this->assertEquals(1, $entity->reference_id);
        $this->assertEquals("levels", $entity->reference_table);
    }

    /** @test */
    public function get_stadia_plant_id_when_set()
    {
        $entity = StadiaLevel::create([
            'name' => 'Test',
            'stadia_plant_id' => 1
        ]);

        $this->assertEquals("Test", $entity->name);
        $this->assertEquals(1, $entity->stadia_plant_id);
    }

}
