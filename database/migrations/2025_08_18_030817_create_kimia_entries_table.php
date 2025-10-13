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
        Schema::create('kimia_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('kimia_forms')->onDelete('cascade');
            $table->foreignId('table_id')->nullable()->constrained('kimia_tables')->nullOnDelete();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kimia_entries');
    }
};
