<?php

namespace JohanKladder\Stadia\Tests\Feature\Facades\StadiaFacade;

use JohanKladder\Stadia\Database\Seeds\CountriesTableSeeder;
use JohanKladder\Stadia\Exceptions\CountryNotFoundException;
use JohanKladder\Stadia\Facades\Stadia;
use JohanKladder\Stadia\Models\Country;
use JohanKladder\Stadia\Tests\TestCase;

class StadiaFacadeGetCountryLocationFeatureTest extends TestCase
{

    /** @test */
    public function get_country_empty_tables()
    {
        $this->expectException(CountryNotFoundException::class);
        Stadia::getCountry(
            "NL"
        );
    }

    /** @test */
    public function get_country_not_found()
    {
        $this->expectException(CountryNotFoundException::class);

        Country::create([
            'code' => "BE"
        ]);

        Stadia::getCountry(
            "NL"
        );
    }

    /** @test */
    public function get_country_when_seeded()
    {

        $this->seed(CountriesTableSeeder::class);

        $country = Stadia::getCountry(
            "NL"
        );

        $this->assertEquals("NL", $country->code);
    }

}
