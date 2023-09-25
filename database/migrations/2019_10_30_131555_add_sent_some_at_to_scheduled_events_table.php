<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSentSomeAtToScheduledEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('scheduled_events', static function (Blueprint $table) {
            $table->timestamp('primer_sent_at')->nullable();
            $table->timestamp('content_sent_at')->nullable();
            $table->timestamp('reflection_sent_at')->nullable();
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
            $table->dropColumn('primer_sent_at');
            $table->dropColumn('content_sent_at');
            $table->dropColumn('reflection_sent_at');
        });
    }
}
