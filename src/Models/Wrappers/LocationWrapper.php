<?php


namespace JohanKladder\Stadia\Models\Wrappers;


class LocationWrapper
{

    public $latitude;
    public $longitude;
    public $countryCode;

    public function __construct($countryCode = null, $latitude = null, $longitude = null)
    {
        $this->countryCode = $countryCode;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

}
