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
        Schema::create('official_data', function (Blueprint $table) {
            $table->id();
            $table->string('staff_name');
            $table->string('nip')->unique();
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('rank')->nullable();
            $table->string('position')->nullable();
            $table->string('work_unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_data');
    }
};
