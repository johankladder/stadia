<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStadiaLevelDurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stadia_level_durations', function (Blueprint $table) {
            $table->id();
            $table->integer("duration");
            $table->bigInteger('stadia_level_id')->nullable();
            $table->bigInteger('climate_code_id')->nullable();
            $table->bigInteger('country_id')->nullable();
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
        Schema::dropIfExists("stadia_level_durations");
    }
}
