<?php

use Illuminate\Support\Facades\Route;
use JohanKladder\Stadia\Http\Controllers\StadiaLevelController;
use JohanKladder\Stadia\Http\Controllers\StadiaPlantController;


Route::get('/', function () {
    return view('stadia::welcome');
});

Route::resource("stadia-plants", StadiaPlantController::class);


Route::resource("stadia-levels", StadiaLevelController::class)->except(['index', 'show']);
Route::get('stadia-levels/index-with-plant/{stadiaPlant}', 'JohanKladder\\Stadia\\Http\\Controllers\\StadiaLevelController@index')->name('stadia-levels.index');


Route::get("calendar/{stadiaPlant}/{country?}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@index')->name('calendar.index');
Route::post("calendar/{stadiaPlant}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@storeCalendarRange')->name('calendar.store');
Route::delete("calendar/{calendarRange}", 'JohanKladder\\Stadia\\Http\\Controllers\\CalendarController@destroy')->name('calendar.destroy');

Route::get("durations/{stadiaLevel}/{country?}", 'JohanKladder\\Stadia\\Http\\Controllers\\DurationController@index')->name('durations.index');
Route::post("durations/{stadiaLevel}", 'JohanKladder\\Stadia\\Http\\Controllers\\DurationController@storeDuration')->name('durations.store');
Route::delete("durations/{stadiaDuration}", 'JohanKladder\\Stadia\\Http\\Controllers\\DurationController@destroy')->name('durations.destroy');

Route::get("stadia-plants/database/sync", "JohanKladder\\Stadia\\Http\\Controllers\\StadiaPlantController@sync")->name('stadia-plants.sync');
Route::get("stadia-levels/database/sync/{stadiaPlant}", "JohanKladder\\Stadia\\Http\\Controllers\\StadiaLevelController@sync")->name('stadia-levels.sync');

Route::get("user-information", "JohanKladder\\Stadia\\Http\\Controllers\\UserInformationController@index")->name('user-information.index');
