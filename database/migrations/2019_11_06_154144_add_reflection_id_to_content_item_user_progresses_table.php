<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReflectionIdToContentItemUserProgressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('content_item_user_progresses', static function (Blueprint $table) {
            $table->unsignedBigInteger('reflection_id')->nullable();

            $table->foreign('reflection_id')
                ->on('user_reflections')
                ->references('id')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('content_item_user_progresses', static function (Blueprint $table) {
            $table->dropForeign(['reflection_id']);
            $table->dropColumn('reflection_id');
        });
    }
}
