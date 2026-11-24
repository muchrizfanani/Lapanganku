<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 150);
            $table->string('sport', 80);
            $table->unsignedInteger('price');
            $table->string('location', 150);
            $table->string('address', 255);
            $table->text('facilities')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->unsignedInteger('reviews')->default(0);
            $table->string('status', 30)->default('available')->index();
            $table->string('gradient', 255)->nullable();
            $table->string('sport_badge', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
