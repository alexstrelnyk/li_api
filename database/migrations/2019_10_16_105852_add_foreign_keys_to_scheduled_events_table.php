<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToScheduledEventsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Artisan::call('clear:scheduled-events');

        Schema::table('scheduled_events', static function (Blueprint $table) {
            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('topic_id')
                ->on('topics')
                ->references('id')
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
        Schema::table('scheduled_events', static function (Blueprint $table) {
            $table->dropForeign(['topic_id']);
            $table->dropForeign(['user_id']);
        });
    }
}
