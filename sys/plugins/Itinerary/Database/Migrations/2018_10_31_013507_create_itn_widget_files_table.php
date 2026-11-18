<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItnWidgetFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('itn_widget_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('widget_id');
            $table->bigInteger('user_id');
            $table->bigInteger('tour_id')->nullable();
            $table->text('site_url')->nullable();
            $table->text('content')->nullable();
            $table->text('service')->nullable();
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('itn_widget_files');
    }
}
