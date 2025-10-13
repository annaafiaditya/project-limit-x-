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
        Schema::create('kimia_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('kimia_forms')->onDelete('cascade');
            $table->string('role');
            $table->string('name');
            $table->string('jabatan')->nullable();
            $table->string('tanda_tangan')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kimia_signatures');
    }
};
