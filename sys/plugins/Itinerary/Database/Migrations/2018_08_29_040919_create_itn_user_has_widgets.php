<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItnUserHasWidgets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itn_user_has_widgets', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable(false);
            $table->integer('widget_id')->nullable(false);
            $table->tinyInteger('status');
            $table->text('working_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itn_user_has_widgets');
    }
}
