<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Membuat tabel jenis_pelatihan dengan primary key custom
    public function up(): void
    {
        Schema::create('jenis_pelatihan', function (Blueprint $table) {
            $table->string('id_jenis')->primary();
            $table->string('nama_jenis');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    // Menghapus tabel jenis_pelatihan
    public function down(): void
    {
        Schema::dropIfExists('jenis_pelatihan');
    }
};
