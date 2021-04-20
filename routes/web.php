<?php

use Illuminate\Support\Facades\Route;
use JohanKladder\Stadia\Http\Controllers\StadiaPlantController;


Route::get('/', function () {
    return view('stadia::welcome');
});

Route::resource("/stadia-plants", StadiaPlantController::class);

Route::get("calendar/{stadiaPlant}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@index')->name('calendar.index');
Route::post("calendar/{stadiaPlant}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@storeCalendarRange')->name('calendar.store');
Route::post("calendar/get-with-country/{stadiaPlant}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@indexCalendarRanges')->name('calendar.withCountry');
Route::delete("calendar/{calendarRange}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@destroy')->name('calendar.destroy');
Route::get("stadia-plants/database/sync", "JohanKladder\\Stadia\\Http\\Controllers\\StadiaPlantController@sync")->name('stadia-plants.sync');
