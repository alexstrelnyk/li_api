<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTopicOverviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules_topic_overview', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('overview_at')->nullable();
            $table->timestamp('start_overview_date')->nullable();
            $table->timestamp('end_overview_date')->nullable();
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
        Schema::dropIfExists('schedules_topic_overview');
    }
}
