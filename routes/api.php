<?php


Route::get('calendar/{country?}/{latitude?}/{longitude?}', 'JohanKladder\\Stadia\\Http\\Controllers\\Api\\StadiaApiController@calendar');
Route::get('calendar-plant/{stadiaPlant}{country?}/{latitude?}/{longitude?}', 'JohanKladder\\Stadia\\Http\\Controllers\\Api\\StadiaApiController@calendarWithPlant');
