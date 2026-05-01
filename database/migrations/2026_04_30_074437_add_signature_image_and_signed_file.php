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
        Schema::table('official_data', function (Blueprint $table) {
            $table->string('signature_image')->nullable()->after('work_unit');
        });
 
        Schema::table('signature_requests', function (Blueprint $table) {
            $table->string('signed_filename')->nullable()->after('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_data', function (Blueprint $table) {
            $table->dropColumn('signature_image');
        });

        Schema::table('signature_requests', function (Blueprint $table) {
            $table->dropColumn('signed_filename');
        });
    }
};
