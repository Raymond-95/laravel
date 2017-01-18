<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('triprequests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('requested_by')->unsigned();
            $table->string('status')->default('pending');
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
        Schema::drop('triprequests');
    }
}
