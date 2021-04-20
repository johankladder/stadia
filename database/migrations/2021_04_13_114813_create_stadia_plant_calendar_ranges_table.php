<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStadiaPlantCalendarRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stadia_plant_calendar_ranges', function (Blueprint $table) {
            $table->id();
            $table->dateTime('range_from')->nullable();
            $table->dateTime('range_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stadia_plant_calendar_ranges');
    }
}
