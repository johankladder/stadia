<?php


Route::get('calendar/{country?}/{climateCode?}', 'JohanKladder\\Stadia\\Http\\Controllers\\Api\\StadiaApiController@calendar');
