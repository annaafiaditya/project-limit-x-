<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mikrobiologi_forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('no');
            $table->string('judul_tabel')->nullable();
            $table->date('tgl_inokulasi');
            $table->date('tgl_pengamatan');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mikrobiologi_forms');
    }
}; 