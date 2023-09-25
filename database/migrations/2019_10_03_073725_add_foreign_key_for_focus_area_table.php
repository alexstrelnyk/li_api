<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyForFocusAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('focus_area', function (Blueprint $table) {
            $table->foreign('focus_id')->references('id')->on('focuses');
            $table->foreign('program_id')->references('id')->on('programs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('focus_area', function (Blueprint $table) {
            $table->dropForeign('focus_area_focus_id_foreign');
            $table->dropForeign('focus_area_program_id_foreign');
        });
    }
}
