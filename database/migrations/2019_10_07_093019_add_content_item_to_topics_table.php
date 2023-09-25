<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentItemToTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('topics', static function (Blueprint $table) {
            $table->unsignedBigInteger('content_item_id')->nullable();

            $table->foreign('content_item_id')
                ->on('content_items')
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
        Schema::table('topics', static function (Blueprint $table) {

            $table->dropForeign('topics_content_item_id_foreign');
            $table->dropColumn('content_item_id');
        });
    }
}
