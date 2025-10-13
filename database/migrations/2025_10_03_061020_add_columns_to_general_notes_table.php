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
        if (!Schema::hasTable('general_notes')) {
            return;
        }
        Schema::table('general_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('general_notes', 'content')) {
                $table->text('content')->nullable();
            }
            if (!Schema::hasColumn('general_notes', 'last_edited_by')) {
                $table->unsignedBigInteger('last_edited_by')->nullable();
            }
            if (!Schema::hasColumn('general_notes', 'last_edited_role')) {
                $table->string('last_edited_role')->nullable();
            }
            if (!Schema::hasColumn('general_notes', 'last_edited_at')) {
                $table->timestamp('last_edited_at')->nullable();
            }
            if (Schema::hasColumn('general_notes', 'last_edited_by')) {
                $table->foreign('last_edited_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_notes', function (Blueprint $table) {
            $table->dropForeign(['last_edited_by']);
            $table->dropColumn(['content', 'last_edited_by', 'last_edited_role', 'last_edited_at']);
        });
    }
};
