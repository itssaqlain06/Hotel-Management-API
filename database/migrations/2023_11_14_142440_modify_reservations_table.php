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
        Schema::table('reservations', function (Blueprint $table) {
            // Check if the column exists before adding it
            if (!Schema::hasColumn('reservations', 'booking_id')) {
                $table->unsignedBigInteger('booking_id')->nullable();
                $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            }
            $table->string('status');

            // Remove columns
            // $table->dropColumn(['payment_method', 'payment_status', 'total_amount','additional_info','special_request']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Add back the removed columns
            // $table->enum('payment_status', ['pending', 'completed'])->default('pending');
            // $table->enum('payment_method', ['Cash', 'JazzCash', 'bank_transfer'])->default('Cash');
            // $table->decimal('total_amount', 10, 2);

            // Remove the new column
            // $table->dropForeign(['booking_id']);
            // $table->dropColumn('booking_id');
        });
    }
};
