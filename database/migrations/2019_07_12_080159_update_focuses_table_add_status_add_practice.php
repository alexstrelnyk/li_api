<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFocusesTableAddStatusAddPractice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('focuses', function (Blueprint $table) {
            $table->bigInteger('practice')->nullable();
            $table->integer('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('focuses', function (Blueprint $table) {

            $table->dropColumn('practice');
            $table->dropColumn('status');
        });
    }
}
