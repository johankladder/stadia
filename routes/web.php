<?php

use Illuminate\Support\Facades\Route;
use JohanKladder\Stadia\Http\Controllers\StadiaLevelController;
use JohanKladder\Stadia\Http\Controllers\StadiaPlantController;


Route::get('/', function () {
    return view('stadia::welcome');
});

Route::resource("stadia-plants", StadiaPlantController::class);
Route::resource("stadia-levels", StadiaLevelController::class);

Route::get("calendar/{stadiaPlant}/{country?}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@index')->name('calendar.index');
Route::post("calendar/{stadiaPlant}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@storeCalendarRange')->name('calendar.store');
Route::delete("calendar/{calendarRange}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@destroy')->name('calendar.destroy');
Route::get("stadia-plants/database/sync", "JohanKladder\\Stadia\\Http\\Controllers\\StadiaPlantController@sync")->name('stadia-plants.sync');
