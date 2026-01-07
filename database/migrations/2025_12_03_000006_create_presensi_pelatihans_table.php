<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Membuat tabel presensi_pelatihan dengan foreign key ke jadwal_pelatihan, karyawan, dan users
    public function up(): void
    {
        Schema::create('presensi_pelatihans', function (Blueprint $table) {
            $table->id('id_presensi');
            $table->unsignedBigInteger('id_jadwal');
            $table->unsignedBigInteger('id_karyawan');
            $table->string('status_kehadiran')->nullable();
            $table->dateTime('waktu_presensi')->nullable();
            $table->string('bukti_kehadiran')->nullable();
            $table->string('dicatat_oleh')->nullable();
            $table->timestamps();

            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal_pelatihans')->onDelete('cascade');
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawans')->onDelete('cascade');
            $table->foreign('dicatat_oleh')->references('email')->on('users')->onDelete('set null');

        });
    }


    /**
     * Reverse the migrations.
     */
    // Menghapus tabel presensi_pelatihan
    public function down(): void
    {
        Schema::dropIfExists('presensi_pelatihans');
    }
};
