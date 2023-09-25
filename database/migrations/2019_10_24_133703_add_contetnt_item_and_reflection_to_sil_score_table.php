<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContetntItemAndReflectionToSilScoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('sil_score', static function (Blueprint $table) {
            $table->unsignedBigInteger('content_item_id')->nullable();
            $table->unsignedBigInteger('reflection_id')->nullable();

            $table->foreign('content_item_id')
                ->on('content_items')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('reflection_id')
                ->on('user_reflections')
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
        Schema::table('sil_score', static function (Blueprint $table) {
            $table->dropForeign(['content_item_id']);
            $table->dropForeign(['reflection_id']);

            $table->dropColumn('content_item_id');
            $table->dropColumn('reflection_id');
        });
    }
}
