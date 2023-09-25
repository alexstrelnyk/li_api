<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToUserReflectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('user_reflections', static function (Blueprint $table) {
            $table->unsignedBigInteger('content_item_id')->change();
            $table->unsignedBigInteger('user_id')->change();

            $table->foreign('content_item_id')
                ->on('content_items')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->on('users')
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
        Schema::table('user_reflections', static function (Blueprint $table) {
            $table->dropForeign('user_reflections_content_item_id_foreign');
        });
    }
}
