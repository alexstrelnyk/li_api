<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProgramIdToUserReflectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('user_reflections', static function (Blueprint $table) {
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('user_progress_id')->nullable();
        });

        Artisan::call('clear:user-reflections');

        Schema::table('user_reflections', static function (Blueprint $table) {
            $table->foreign('program_id')
                ->on('programs')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('user_progress_id')
                ->on('content_item_user_progresses')
                ->references('id')
                ->onDelete('cascade');

            $table->unsignedBigInteger('program_id')->change();
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
            $table->dropForeign(['program_id']);
            $table->dropForeign(['user_progress_id']);
            $table->dropColumn('program_id');
            $table->dropColumn('user_progress_id');
        });
    }
}
