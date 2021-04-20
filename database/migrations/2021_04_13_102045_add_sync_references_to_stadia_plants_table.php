<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyncReferencesToStadiaPlantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stadia_plants', function (Blueprint $table) {
            $table->unsignedInteger('reference_id')->nullable();
            $table->string('reference_table')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stadia_plants', function (Blueprint $table) {
            //
        });
    }
}
