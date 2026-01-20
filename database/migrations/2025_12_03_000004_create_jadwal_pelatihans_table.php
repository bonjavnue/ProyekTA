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
        Schema::create('jadwal_pelatihans', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->string('id_jenis');
            $table->date('tanggal_pelaksanaan');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('tempat');
            $table->dateTime('tenggat_presensi')->nullable(); //tambahan nullable
            $table->string('link_presensi')->nullable();
            $table->string('qr_code')->nullable();
            $table->dateTime('waktu_mulai_presensi')->nullable();
            $table->dateTime('waktu_berakhir_presensi')->nullable();
            $table->enum('status', ['draft', 'published', 'selesai'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreign('id_jenis')->references('id_jenis')->on('jenis_pelatihan')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelatihans');
    }
};
