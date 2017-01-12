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
            $table->string('destination')->default(null);
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('information', 150)->default(null);
            $table->string('role')->default(null);
            $table->string('user_id')->default(null);
            $table->timestamp('created_at')->nullableTimestamps();
            $table->timestamp('updated_at')->nullable();
            $table->string('requested_by')->default(null);
            $table->timestamp('approved_at')->nullable();
            $table->string('status')->default("available");
            $table->timestamp('departed_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('guardian')->default(null);
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
