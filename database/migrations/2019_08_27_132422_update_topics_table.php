<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('topics', static function (Blueprint $table) {
            $table->renameColumn('user_can_practice', 'has_practice');
            $table->renameColumn('primer_description', 'primer_content');
            $table->renameColumn('primer_notification_text', 'primer_notification');
            $table->renameColumn('information_description', 'info_content');
            $table->renameColumn('information_notification_text', 'info_notification');
            $table->string('info_image', 2048);
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
            $table->renameColumn('has_practice', 'user_can_practice');
            $table->renameColumn('primer_content', 'primer_description');
            $table->renameColumn('primer_notification', 'primer_notification_text');
            $table->renameColumn('info_content', 'information_description');
            $table->renameColumn('info_notification', 'information_notification_text');
            $table->dropColumn('info_image');
        });
    }
}
