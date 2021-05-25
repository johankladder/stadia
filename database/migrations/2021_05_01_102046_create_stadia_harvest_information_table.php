<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStadiaHarvestInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stadia_harvest_information', function (Blueprint $table) {
            $table->id();

            $table->dateTime('sow_date')->nullable();
            $table->dateTime('harvest_date')->nullable();

            $table->foreignId('stadia_plant_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('country_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('climate_code_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

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
        Schema::dropIfExists("stadia_harvest_information");
    }
}
