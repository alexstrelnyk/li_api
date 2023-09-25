<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProgramIdToContentItemUserProgressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('content_item_user_progresses', static function (Blueprint $table) {
            $table->unsignedBigInteger('program_id')->nullable();
        });

        Artisan::call('clear:user-progresses');

        Schema::table('content_item_user_progresses', static function (Blueprint $table) {
            $table->foreign('program_id')
                ->on('programs')
                ->references('id')
                ->onDelete('cascade')
            ;

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
        Schema::table('content_item_user_progresses', static function (Blueprint $table) {
            $table->dropColumn('program_id');
            $table->dropForeign(['program_id']);
        });
    }
}
