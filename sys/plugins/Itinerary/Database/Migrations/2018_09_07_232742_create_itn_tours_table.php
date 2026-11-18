<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItnToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itn_tours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('prototype_id')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->bigInteger('guider_id')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('itn_tours');
    }
}
