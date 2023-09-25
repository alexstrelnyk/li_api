<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FocusAreasTopics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('focus_areas_topics', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('focus_area_id')->unsigned()->index();
            $table->bigInteger('topic_id')->unsigned()->index();
            $table->integer('status');
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
        Schema::dropIfExists('focus_areas_topics');
    }
}
