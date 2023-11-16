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
        Schema::table('bookings', function (Blueprint $table) {
            // Check if the columns exist before adding them
            if (!Schema::hasColumn('bookings', 'number_of_guests')) {
                $table->unsignedInteger('number_of_guests');
            }

            if (!Schema::hasColumn('bookings', 'status')) {
                $table->boolean('status')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop the columns in the down method if needed
            $table->dropColumn(['number_of_guests', 'status']);
        });
    }
};
