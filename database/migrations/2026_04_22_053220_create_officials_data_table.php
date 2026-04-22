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
        Schema::create('officials_data', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nip')->unique();
            $table->string('email');
            $table->string('phone_number');
            $table->string('rank')->nullable();
            $table->string('work_unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officials_data');
    }
};
