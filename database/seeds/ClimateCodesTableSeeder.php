<?php

namespace JohanKladder\Stadia\Database\Seeds;


use Illuminate\Database\Seeder;
use JohanKladder\Stadia\Models\ClimateCode;

class ClimateCodesTableSeeder extends Seeder
{

    private $codes = [
        "Af",
        "Am",
        "Aw",
        "BWh",
        "BWk",
        "BSh",
        "BSk",
        "Csa",
        "Csb",
        "Cwa",
        "Cwb",
        "Cwc",
        "Cfa",
        "Cfb",
        "Cfc",
        "Dsa",
        "Dsb",
        "Dsc",
        "Dsd",
        "Dwa",
        "Dwb",
        "Dwc",
        "Dwd",
        "Dfa",
        "Dfb",
        "Dfc",
        "Dfd",
        "ET",
        "EF"
    ];

    public function run()
    {
        foreach ($this->codes as $code) {
            ClimateCode::firstOrCreate([
                'code' => $code
            ]);
        }
    }
}
