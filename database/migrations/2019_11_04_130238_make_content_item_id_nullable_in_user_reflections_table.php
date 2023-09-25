<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeContentItemIdNullableInUserReflectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('user_reflections', static function (Blueprint $table) {
            $table->unsignedBigInteger('content_item_id')->nullable()->change();
        });

        Artisan::call('clear:onboarding-reflections');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('user_reflections', static function (Blueprint $table) {
            $table->unsignedBigInteger('content_item_id')->change();

        });
    }
}
