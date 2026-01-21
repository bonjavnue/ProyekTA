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
        Schema::table('presensi_pelatihans', function (Blueprint $table) {
            // Drop foreign key constraint dari dicatat_oleh
            $table->dropForeign(['dicatat_oleh']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi_pelatihans', function (Blueprint $table) {
            // Restore foreign key
            $table->foreign('dicatat_oleh')->references('email')->on('users')->onDelete('set null');
        });
    }
};
