<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mikrobiologi_forms', function (Blueprint $table) {
            // $table->string('judul_tabel')->nullable()->after('no');
        });
    }

    public function down(): void
    {
        Schema::table('mikrobiologi_forms', function (Blueprint $table) {
            // $table->dropColumn('judul_tabel');
        });
    }
}; 