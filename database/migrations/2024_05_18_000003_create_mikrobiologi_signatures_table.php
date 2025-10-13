<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mikrobiologi_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('mikrobiologi_forms')->onDelete('cascade');
            $table->enum('role', ['technician', 'staff', 'supervisor']);
            $table->string('name');
            $table->string('jabatan')->nullable();
            $table->enum('status', ['accept', 'reject'])->default('accept');
            $table->string('tanda_tangan')->nullable(); // path file/image
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrobiologi_signatures');
    }
}; 