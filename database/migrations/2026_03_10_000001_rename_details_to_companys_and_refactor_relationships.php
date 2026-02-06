<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename the table [Addendum 1.a]
        Schema::rename('customer_details', 'companys');

        Schema::table('companys', function (Blueprint $table) {
            // 2. Move catalog_id from users to companys [Addendum 1.b]
            $table->foreignId('catalog_id')->nullable()->after('id')->constrained('catalogs')->onDelete('set null');

            // 3. Add parent_id to companys to manage business hierarchy directly [Addendum 3.c]
            $table->foreignId('parent_id')->nullable()->after('catalog_id')->constrained('companys')->onDelete('set null');
        });

        // 4. Data Migration: Move existing catalog_id assignments from users to their respective companies
        $usersWithCatalogs = DB::table('users')->whereNotNull('catalog_id')->whereNotNull('customer_detail_id')->get();
        foreach ($usersWithCatalogs as $user) {
            DB::table('companys')->where('id', $user->customer_detail_id)->update(['catalog_id' => $user->catalog_id]);
        }

        // 5. Refactor Users table [Addendum 2.a]
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['catalog_id']);
            $table->dropColumn('catalog_id');
            $table->renameColumn('customer_detail_id', 'company_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('company_id', 'customer_detail_id');
            $table->unsignedBigInteger('catalog_id')->nullable();
            $table->foreign('catalog_id')->references('id')->on('catalogs')->onDelete('set null');
        });

        Schema::table('companys', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'catalog_id']);
        });

        Schema::rename('companys', 'customer_details');
    }
};
