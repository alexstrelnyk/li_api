<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotificationTextColumnsToContentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('content_items', static function (Blueprint $table) {
            $table->text('primer_notification_text')->nullable();
            $table->text('content_notification_text')->nullable();
            $table->text('reflection_notification_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('content_items', static function (Blueprint $table) {
            $table->dropColumn([
                'primer_notification_text',
                'content_notification_text',
                'reflection_notification_text'
            ]);
        });
    }
}
