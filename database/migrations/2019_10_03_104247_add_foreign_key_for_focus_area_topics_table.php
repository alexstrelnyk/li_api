<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyForFocusAreaTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('focus_areas_topics', function (Blueprint $table) {
            $table->foreign('focus_area_id')->references('id')->on('focus_area')
                ->onDelete('cascade');

            $table->foreign('topic_id')->references('id')->on('topics')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('focus_areas_topics', function (Blueprint $table) {
            $table->dropForeign('focus_areas_topics_focus_area_id_foreign');
            $table->dropForeign('focus_areas_topics_topic_id_foreign');
        });
    }
}
