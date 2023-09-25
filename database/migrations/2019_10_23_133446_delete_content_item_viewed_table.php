<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteContentItemViewedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('content_item_viewed');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::create('content_item_viewed', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('content_item_id');
            $table->bigInteger('user_id');
            $table->boolean('primerViewed')->nullable();
            $table->boolean('infoViewed')->nullable();
            $table->boolean('reflectionViewed')->nullable();
            $table->timestamps();
        });
    }
}
