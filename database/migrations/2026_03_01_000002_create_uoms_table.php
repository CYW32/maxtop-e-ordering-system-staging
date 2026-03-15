<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('uom_name'); // e.g., "Carton", "Pack" [Addendum 3.a]
            $table->integer('rate_qty'); // e.g., 24
            $table->decimal('price', 10, 2); // Hidden from customers
            $table->softDeletes(); // For "Hide" rule [Addendum 3.c]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uoms');
    }
};
