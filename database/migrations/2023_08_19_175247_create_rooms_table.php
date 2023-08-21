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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('room_no');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('capacity');
            $table->enum('type', ['standard', 'deluxe', 'suite']);
            $table->foreignId('hotel_id')->constrained('hotels');

            $table->boolean('is_available')->default(true);
            $table->boolean('is_smoking_allowed')->default(false);
            $table->boolean('has_balcony')->default(true);
            $table->boolean('has_pool_access')->default(true);
            $table->boolean('has_room_service')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
