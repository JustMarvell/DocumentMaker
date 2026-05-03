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
        Schema::create('document_number_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            // e.g "{number}/DPUPR/{roman_month}/{year}"
            // tokens : {number}, {year}, {month}, {roman_month}
            $table->string('format')->default('{number}/DPUPR/{roman_month}/{year}');
            $table->unsignedInteger('current_number')->default(0);
            $table->unsignedInteger('number_padding')->default(3);      // 3 -> 045
            $table->enum('reset_on', ['never', 'yearly', 'monthly'])->default('yearly');
            $table->unsignedSmallInteger('last_reset_year')->nullable();
            $table->unsignedSmallInteger('last_reset_month')->nullable();
            $table->string('field_key')->default('letter_number');  // field to inject
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_number_counters');
    }
};
