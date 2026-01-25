<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Membuat tabel karyawan dengan foreign key ke bagian
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->string('id_karyawan')->primary();
            $table->string('nik')->unique();
            $table->string('nama_karyawan');
            $table->bigInteger('id_bagian')->unsigned();
            $table->string('status_karyawan');
            $table->string('no_telepon')->nullable();
            $table->foreign('id_bagian')->references('id_bagian')->on('bagian')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    // Menghapus tabel karyawan
    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
