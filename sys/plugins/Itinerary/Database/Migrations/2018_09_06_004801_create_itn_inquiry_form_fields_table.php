<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItnInquiryFormFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itn_inquiry_form_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('form_id')->nullable(true);
            $table->string('name');
            $table->string('type');
            $table->string('placeholder')->nullable(true);
            $table->string('attr')->nullable(true);
            $table->integer('priority')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('itn_inquiry_form_fields');
    }
}
