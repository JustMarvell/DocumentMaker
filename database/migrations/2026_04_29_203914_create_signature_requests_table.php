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
        Schema::create('signature_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('document_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('official_id')->nullable()->constrained('official_data')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('token', 64)->unique();
            $table->text('notes')->nullable();

            $table->timestamp('requested_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });

        Schema::table('document_types', function(Blueprint $table) {
            $table->boolean('signature_enabled')->default(false)->after('preview_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signature_requests');

        Schema::table('document_types', function(Blueprint $table) {
            $table->dropColumn('signature_enabled');
        });
    }
};
