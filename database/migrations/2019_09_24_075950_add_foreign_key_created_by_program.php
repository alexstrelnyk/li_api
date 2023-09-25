<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyCreatedByProgram extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('programs')->delete();
        Schema::table('programs', static function (Blueprint $blueprint) {
            $blueprint->bigInteger('created_by')->unsigned()->nullable();
            $blueprint->bigInteger('updated_by')->unsigned()->nullable();

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programs', static function (Blueprint $blueprint) {
            $blueprint->dropForeign(['created_by']);
            $blueprint->dropForeign(['updated_by']);
        });
    }
}
