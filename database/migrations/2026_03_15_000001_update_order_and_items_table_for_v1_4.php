<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Order Status Enum
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });

        // 2. Update Order Items for UOM Snapshots [Addendum 5.a]
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('uom_id')->nullable()->after('item_id')->constrained('uoms')->onDelete('set null');
            $table->string('snapshot_uom_name')->nullable()->after('snapshot_name');
            $table->integer('snapshot_uom_rate')->nullable()->after('snapshot_uom_name');
        });

        // 3. ARCHITECTURE FIX: Drop foreign key before dropping column [Addendum 2.a]
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_id']); // Must drop constraint first
            $table->dropColumn('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['uom_id']);
            $table->dropColumn(['uom_id', 'snapshot_uom_name', 'snapshot_uom_rate']);
        });
    }
};
