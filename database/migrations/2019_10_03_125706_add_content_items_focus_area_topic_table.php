<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentItemsFocusAreaTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('focus_areas_topics_content_items', static function (Blueprint $table) {
            $table->bigInteger('focus_area_topics_id')->unsigned()->index();
            $table->bigInteger('content_item_id')->unsigned()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('focus_areas_topics_content_items');
    }
}
