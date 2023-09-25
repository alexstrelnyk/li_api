<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProgramContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('program_contents', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('content_item_id');
            $table->unsignedBigInteger('sort_order');
            $table->auditable();
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
        Schema::dropIfExists('program_contents');
    }
}
