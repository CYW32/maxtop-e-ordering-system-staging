<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->enum('status', ['active', 'deactive'])->default('active')->after('price');
        });
        Schema::table('catalogs', function (Blueprint $table) {
            $table->enum('status', ['active', 'deactive'])->default('active')->after('name');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('status', ['active', 'deactive'])->default('active')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('items', fn ($table) => $table->dropColumn('status'));
        Schema::table('catalogs', fn ($table) => $table->dropColumn('status'));
        Schema::table('categories', fn ($table) => $table->dropColumn('status'));
    }
};
