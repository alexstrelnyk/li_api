<?php
declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignToClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        User::where('client_id', '!=', null)->get()->each(static function (User $user) {
            if ($user->client === null) {
                dump('User '.$user->id.' deleted');
                $user->delete();
            }
        });

        Schema::table('users', static function (Blueprint $table) {
            $table->bigInteger('client_id')->unsigned()->nullable()->index()->change();

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
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
        Schema::table('users', static function (Blueprint $table) {
            $table->dropForeign(['client_id']);
        });
    }
}
