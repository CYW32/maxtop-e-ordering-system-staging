<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fulfills Addendum Section 2: Enhanced Order Security
     * Adds tracking for the "Request-Response" cancellation protocol.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Stores the ID of the CS Staff who initiated the request
            $table->foreignId('cancellation_requested_by')
                ->nullable()
                ->after('internal_notes')
                ->constrained('users')
                ->onDelete('set null');

            // Stores the mandatory reason provided by the staff member
            $table->text('cancellation_request_reason')
                ->nullable()
                ->after('cancellation_requested_by');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cancellation_requested_by']);
            $table->dropColumn(['cancellation_requested_by', 'cancellation_request_reason']);
        });
    }
};
