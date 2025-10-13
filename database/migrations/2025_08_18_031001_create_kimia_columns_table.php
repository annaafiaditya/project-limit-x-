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
        Schema::create('kimia_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('kimia_forms')->onDelete('cascade');
            $table->foreignId('table_id')->nullable()->constrained('kimia_tables')->nullOnDelete();
            $table->string('nama_kolom');
            $table->string('tipe_kolom');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kimia_columns');
    }
};
