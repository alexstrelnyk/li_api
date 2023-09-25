<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSchedulesTopicOverviewTableAddFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('schedules_topic_overview', static function (Blueprint $table) {

            // FK with users
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');

            // FK with focuses
            $table->unsignedBigInteger('focus_id');
            $table->foreign('focus_id')->references('id')->on('focuses')
                ->onDelete('cascade');

            // FK with focuses
            $table->unsignedBigInteger('topic_id');
            $table->foreign('topic_id')->references('id')->on('topics')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('schedules_topic_overview', static function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropColumn('topic_id');
            $table->dropForeign(['focus_id']);
            $table->dropColumn('focus_id');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
