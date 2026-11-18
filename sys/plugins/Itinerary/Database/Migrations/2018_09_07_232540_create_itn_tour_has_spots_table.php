<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItnTourHasSpotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itn_tour_has_spots', function (Blueprint $table) {
            $table->bigInteger('tour_id');
            $table->bigInteger('spot_id');
            $table->integer('day_order')->nullable();
            $table->integer('visit_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itn_tour_has_spots');
    }
}
