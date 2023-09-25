<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAboutMeSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('about_me_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->boolean('event_practice_email');
            $table->boolean('event_practice_notification');
            $table->boolean('random_acts_email');
            $table->boolean('random_acts_notification');
            $table->boolean('login_streak_email');
            $table->boolean('login_streak_notification');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('about_me_settings');
    }
}
