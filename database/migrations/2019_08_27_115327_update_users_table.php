<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->renameColumn('name', 'first_name');
            $table->string('last_name');
            $table->string('job_role')->nullable();
            $table->string('job_dept')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->smallInteger('permission');
            $table->string('tip_time')->nullable();
            $table->string('timezone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', static function (Blueprint $table) {
            $table->renameColumn('first_name', 'name');
            $table->dropColumn('last_name');
            $table->dropColumn('job_role');
            $table->dropColumn('job_dept');
            $table->dropColumn('client_id');
            $table->dropColumn('permission');
            $table->dropColumn('tip_time');
            $table->dropColumn('timezone');
        });
    }
}
