<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update customer_details to include business codes [Addendum 1.c]
        Schema::table('customer_details', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Remove old 1:1 link
            $table->dropColumn('user_id');
            $table->string('company_code')->nullable()->unique()->after('id'); // For HQ
            $table->string('branch_code')->nullable()->unique()->after('company_code'); // For Branches
        });

        // 2. Update users to link to a shared Customer Detail record [Addendum 1.a]
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('customer_detail_id')->nullable()->constrained('customer_details')->onDelete('set null')->after('parent_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['customer_detail_id']);
            $table->dropColumn('customer_detail_id');
        });
        Schema::table('customer_details', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->dropColumn(['company_code', 'branch_code']);
        });
    }
};
