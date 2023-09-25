<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFocusProgramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('focus_program', static function (Blueprint $table) {
            $table->bigInteger('focus_id')->unsigned()->index();
            $table->bigInteger('program_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('focus_id')
                ->on('focuses')
                ->references('id')
                ->onDelete('cascade');

            $table->foreign('program_id')
                ->on('programs')
                ->references('id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('focus_program');
    }
}
