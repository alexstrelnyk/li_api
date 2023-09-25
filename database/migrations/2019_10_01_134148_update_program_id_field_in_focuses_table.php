<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProgramIdFieldInFocusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('focuses', static function (Blueprint $table) {
            $table->dropColumn('program_id');
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
            $table->bigInteger('program_id')->unsigned();
        });
    }
}
