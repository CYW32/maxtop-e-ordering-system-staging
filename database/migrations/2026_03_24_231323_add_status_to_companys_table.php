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
        Schema::table('companys', function (Blueprint $table) {
            // Add the status column, defaulting to 'active' so old companies don't break
            $table->string('status')->default('active')->after('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companys', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};