<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveNonNeccessaryFieldsFromTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('topics', static function (Blueprint $table) {
            $table->dropColumn('primer_title');
            $table->dropColumn('primer_content');
            $table->dropColumn('primer_notification');
            $table->dropColumn('info_content');
            $table->dropColumn('info_notification');
            $table->dropColumn('has_reflection');
            $table->dropColumn('reflection_help_text');
            $table->dropColumn('reflection_notification_text');
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
            $table->text('primer_title');
            $table->text('primer_content');
            $table->text('primer_notification');
            $table->text('info_content');
            $table->text('info_notification');
            $table->boolean('has_reflection');
            $table->text('reflection_help_text');
            $table->text('reflection_notification_text');
        });
    }
}
