<?php

namespace JohanKladder\Stadia\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use JohanKladder\Stadia\StadiaPackageServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            StadiaPackageServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {

    }

}
