<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mikrobiologi_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('mikrobiologi_forms')->onDelete('cascade');
            $table->json('data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrobiologi_entries');
    }
}; 