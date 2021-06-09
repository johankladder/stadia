<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IndexOnFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('climate_codes', function (Blueprint $table) {
            $table->index('code');
        });

        Schema::table('stadia_countries', function (Blueprint $table) {
            $table->index('code');
        });

        Schema::table('koepen_locations', function (Blueprint $table) {
            $table->index([
                'latitude',
                'longitude'
            ]);
        });

        Schema::table('stadia_plant_calendar_ranges', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('stadia_countries');
            $table->foreign('climate_code_id')->references('id')->on('climate_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
