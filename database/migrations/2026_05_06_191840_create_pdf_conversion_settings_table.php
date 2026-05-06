<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_conversion_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monthly_limit')->default(250);
            $table->unsignedInteger('used_count')->default(0);
            $table->enum('reset_on', ['monthly', 'manual'])->default('monthly');
            $table->unsignedSmallInteger('last_reset_year')->nullable();
            $table->unsignedSmallInteger('last_reset_month')->nullable();
            $table->string('iloveapi_public_key')->nullable();
            $table->string('iloveapi_secret_key')->nullable();
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('pdf_conversion_settings');
    }
};
