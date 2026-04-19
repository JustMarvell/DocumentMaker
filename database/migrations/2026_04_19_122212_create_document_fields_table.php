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
        Schema::create('document_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();

            // core
            $table->string('field_key');        // jinja2 variable name
            $table->string('label');            // UI label
            $table->enum('field_type', [        
                'text', 'textarea', 'date', 'number',
                'select', 'checkbox', 'repeating_group'
            ])->default('text');
            $table->json('field_options')->nullable();

            // behaviour
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);

            // grouping
            $table->string('section_label')->nullable();

            // repeating_group child fields
            $table->string('group_key')->nullable();
            $table->boolean('is_group_child')->default(false);

            // staff autofill map
            $table->string('staff_autofill_column')->nullable();    // (e.g. 'staff_name', 'nip', 'phone_number', etc dll)

            $table->timestamps();

            // unique field key
            $table->unique(['document_type_id', 'field_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_fields');
    }
};
