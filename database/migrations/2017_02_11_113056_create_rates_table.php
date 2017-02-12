<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');

            $table->Integer('rate');

            $table->integer('rate_to')->unsigned();
            $table->foreign('rate_to')->references('id')->on('users')->onDelete('cascade');

            $table->integer('rate_by')->unsigned();
            $table->foreign('rate_by')->references('id')->on('users')->onDelete('cascade');

            $table->timestamp('created_at')->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rates');
    }
}
