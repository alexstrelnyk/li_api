<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentItemFieldToScheduledEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('scheduled_events', static function (Blueprint $table) {
            $table->bigInteger('content_item_id');
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
            $table->dropColumn('content_item_id');
        });
    }
}
