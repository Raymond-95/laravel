<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->increments('id');

            $table->string('source')->default(null);
            $table->float('source_lat', 10,6)->default(0);
            $table->float('source_lng', 10,6)->default(0);

            $table->string('destination')->default(null);
            $table->float('destination_lat', 10,6)->default(0);
            $table->float('destination_lng', 10,6)->default(0);
            
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('information', 150)->default(null);
            $table->string('role', 9)->default("passenger");

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamp('created_at')->nullableTimestamps();
            $table->timestamp('updated_at')->nullable();
            $table->string('status', 20)->default("available");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trips');
    }
}
