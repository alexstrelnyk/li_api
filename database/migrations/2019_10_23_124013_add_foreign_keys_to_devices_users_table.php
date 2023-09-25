<?php
declare(strict_types=1);

use App\Models\Device;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysToDevicesUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::table('devices_users')->whereNotIn('user_id', User::all()->pluck('id')->toArray())->delete();
        DB::table('devices_users')->whereNotIn('device_id', Device::all()->pluck('id')->toArray())->delete();

        Schema::table('devices_users', static function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->unsignedBigInteger('device_id')->change();

            $table->foreign('user_id')
                ->on('users')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('device_id')
                ->on('devices')
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
        Schema::table('devices_users', static function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['device_id']);
        });
    }
}
