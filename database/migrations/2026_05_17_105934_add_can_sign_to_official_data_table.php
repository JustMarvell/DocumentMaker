<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('official_data', function (Blueprint $table) {
            $table->boolean('can_sign')->default(false)->after('signature_image');
        });
    }
    public function down(): void
    {
        Schema::table('official_data', function (Blueprint $table) {
            $table->dropColumn('can_sign');
        });
    }
};