<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeysForCreatedByColumns extends Migration
{
    /**
     * @var array
     */
    protected $tables = [
        'clients',
        'content_items',
        'focuses',
        'topics',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            DB::table($table)->delete();
            Schema::table($table, static function (Blueprint $blueprint) {
                $blueprint->bigInteger('created_by')->unsigned()->nullable()->change();
                $blueprint->bigInteger('updated_by')->unsigned()->nullable()->change();

                $blueprint->foreign('created_by')
                    ->references(['id'])
                    ->on('users')
                    ->onDelete('SET NULL');
                $blueprint->foreign('updated_by')
                    ->references(['id'])
                    ->on('users')
                    ->onDelete('SET NULL');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, static function (Blueprint $blueprint) {
                $blueprint->dropForeign(['created_by']);
                $blueprint->dropForeign(['updated_by']);
            });
        }
    }
}
