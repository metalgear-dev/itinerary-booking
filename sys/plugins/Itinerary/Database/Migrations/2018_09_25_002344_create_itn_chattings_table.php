<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItnChattingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itn_chattings', function (Blueprint $table) {
            $table->bigInteger('inquiry_id');
            $table->bigInteger('user_id');
            $table->mediumText('content')->nullable(true);
            $table->string('type')->nullable(true);
            $table->string('name')->nullable(true);
            $table->mediumText('avatarUrl')->nullable(true);
            $table->string('ownerId')->nullable(true);
            $table->dateTime('time')->nullable(true);
            $table->dateTime('created_at')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itn_chattings');
    }
}
