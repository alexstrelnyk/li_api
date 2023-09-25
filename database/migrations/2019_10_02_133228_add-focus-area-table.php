<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFocusAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('focus_area', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('program_id')->unsigned()->index();
            $table->bigInteger('focus_id')->unsigned()->index();
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('focus_area');
    }
}
