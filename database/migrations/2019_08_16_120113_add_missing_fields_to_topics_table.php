<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissingFieldsToTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->string('primer_title', 1024)->nullable();
            $table->text('primer_description')->nullable();
            $table->text('primer_notification_text')->nullable();

            $table->text('information_description')->nullable();
            $table->text('information_notification_text')->nullable();

            $table->boolean('has_reflection')->default(false);
            $table->text('reflection_help_text')->nullable();
            $table->text('reflection_notification_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('primer_title');
            $table->dropColumn('primer_description');
            $table->dropColumn('primer_notification_text');
        });
    }
}
