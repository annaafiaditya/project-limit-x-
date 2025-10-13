<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mikrobiologi_observations')) {
            Schema::create('mikrobiologi_observations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('form_id')->constrained('mikrobiologi_forms')->onDelete('cascade');
                $table->date('tanggal');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mikrobiologi_observations')) {
            Schema::dropIfExists('mikrobiologi_observations');
        }
    }
};


