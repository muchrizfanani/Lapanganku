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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('legacy_id', 100)->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->string('venue_name', 150);
            $table->string('venue_location', 150);
            $table->string('sport', 80);
            $table->string('player_name', 120);
            $table->string('player_phone', 32);
            $table->date('booking_date');
            $table->json('slots');
            $table->unsignedSmallInteger('total_slots')->default(0);
            $table->unsignedInteger('price_per_hour');
            $table->unsignedInteger('total_price');
            $table->string('status', 30)->default('pending')->index();
            $table->string('payment_method', 100)->default('Bayar di Tempat (COD)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
