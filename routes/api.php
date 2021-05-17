<?php


Route::get('calendar/{country?}/{climateCode?}', 'JohanKladder\\Stadia\\Http\\Controllers\\Api\\StadiaApiController@calendar');
Route::get('calendar-plant/{stadiaPlant}{country?}/{climateCode?}', 'JohanKladder\\Stadia\\Http\\Controllers\\Api\\StadiaApiController@calendarWithPlant');
