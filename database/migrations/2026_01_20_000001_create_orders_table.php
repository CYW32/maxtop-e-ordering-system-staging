<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable()->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('handler_id')->nullable()->constrained('users')->onDelete('set null');

            // Fulfills Section 4: Lifecycle Statuses
            $table->enum('status', ['draft', 'pending', 'approved', 'in_transit', 'completed', 'cancelled'])->default('draft');

            // Fulfills Section 4.6 & 6: Cancellation and Notes
            $table->text('cancellation_reason')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('restrict');

            // Fulfills Section 3C: Item Name Snapshot Logic
            // This stores the name at the moment of approval/submission
            $table->string('snapshot_name');

            $table->integer('quantity');
            $table->decimal('price_at_order', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
