<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToFocuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('focuses', function (Blueprint $table) {
            $table->string('image_url', 2048);
            $table->auditable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('focuses', static function (Blueprint $table) {
            $table->dropColumn('image_url');
            $table->dropAuditable();
        });
    }
}
