<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnsInFocusAreasTopicsContentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Artisan::call('clear:topic-areas-content-items');

        Schema::table('focus_areas_topics_content_items', static function (Blueprint $table) {
            $table->dropIndex(['content_item_id']);
            $table->dropIndex(['focus_area_topics_id']);

            $table->foreign('content_item_id', 'content_item_id_foreign')
                ->on('content_items')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('focus_area_topics_id', 'focus_area_topics_id_foreign')
                ->on('focus_areas_topics')
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
        Schema::table('focus_areas_topics_content_items', static function (Blueprint $table) {
            $table->index(['content_item_id']);
            $table->index(['focus_area_topics_id']);

            $table->dropForeign('content_item_id_foreign');
            $table->dropForeign('focus_area_topics_id_foreign');
        });
    }
}
