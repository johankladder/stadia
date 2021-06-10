<?php

namespace JohanKladder\Stadia\Http\Controllers;


use JohanKladder\Stadia\Charts\HarvestChart;

class UserInformationController extends Controller
{

    public function index()
    {
        return view("stadia::user-information.index", [

        ]);
    }
}
