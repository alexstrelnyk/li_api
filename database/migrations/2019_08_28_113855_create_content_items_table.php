<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('content_items', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedSmallInteger('content_type');
            $table->unsignedBigInteger('focus_id');
            $table->unsignedBigInteger('topic_id');
            $table->string('primer_title');
            $table->text('primer_content');
            $table->unsignedSmallInteger('reading_time');
            $table->string('info_content_image', 2048)->nullable();
            $table->text('info_quick_tip')->nullable();
            $table->text('info_full_content');
            $table->string('info_video_uri', 2048)->nullable();
            $table->string('info_source_title');
            $table->string('info_source_link', 2048);
            $table->boolean('has_reflection')->default(false);
            $table->text('reflection_help_text')->nullable();
            $table->unsignedSmallInteger('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('content_items');
    }
}
