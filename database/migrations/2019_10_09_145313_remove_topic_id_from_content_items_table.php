<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTopicIdFromContentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('content_items', static function (Blueprint $table) {
            $table->dropColumn('topic_id');
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
            $table->unsignedBigInteger('topic_id')->nullable();
        });
    }
}
