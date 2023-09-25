<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::dropIfExists('admins');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::create('admins', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->unique();
            $table->integer('role')->nullable();
            $table->string('token')->unique()->nullable();
            $table->timestamps();
        });
    }
}
