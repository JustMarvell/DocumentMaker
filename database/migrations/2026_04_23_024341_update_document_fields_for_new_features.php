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
        Schema::table('document_fields', function (Blueprint $table) {
            // Change autofill_role from enum to plain string
            // so it can hold any slot_key value
            $table->string('autofill_role')->default('none')->change();

            // Add new field types by modifying the enum
            // SQLite doesn't support ALTER COLUMN for enums,
            // so we use a string column for field_type instead
            // thanks claude
            $table->string('field_type')->default('text')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_fields', function (Blueprint $table) {
            $table->string('autofill_role')->default('none')->change();
            $table->string('field_type')->default('text')->change();
        });
    }
};
