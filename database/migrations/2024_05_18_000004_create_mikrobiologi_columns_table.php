<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('mikrobiologi_columns');
        Schema::create('mikrobiologi_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('mikrobiologi_forms')->onDelete('cascade');
            $table->string('nama_kolom');
            $table->string('tipe_kolom');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrobiologi_columns');
    }
}; 