<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReservationDateToItnInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itn_inquiries', function (Blueprint $table) {
            $table->addColumn('datetime', 'reservation_date')->after('notified_to')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itn_inquiries', function (Blueprint $table) {
            $table->dropColumn('reservation_date');
        });
    }
}
