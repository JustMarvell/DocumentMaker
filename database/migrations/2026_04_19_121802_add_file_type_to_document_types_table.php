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
        Schema::table('document_types', function (Blueprint $table) {
            $table->enum('file_type', ['docx', 'xlsx'])->default('docx')->after('output_filename');
            $table->enum('staff_autofill_role', ['none', 'employee', 'appraiser', 'both'])
                ->default('none')->after('file_type'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn('file_type', 'staff_autofill_role');
        });
    }
};
