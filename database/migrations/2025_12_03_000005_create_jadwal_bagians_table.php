<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Membuat tabel jadwal_bagian dengan foreign key ke jadwal_pelatihan dan bagian
    public function up(): void
    {
        Schema::create('jadwal_bagians', function (Blueprint $table) {
            $table->id('id_jadwalBagian');
            $table->unsignedBigInteger('id_jadwal');
            $table->unsignedBigInteger('id_bagian');
            $table->foreign('id_jadwal')
                  ->references('id_jadwal')
                  ->on('jadwal_pelatihans')
                  ->onDelete('cascade');
            $table->foreign('id_bagian')
                  ->references('id_bagian')
                  ->on('bagian')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    // Menghapus tabel jadwal_bagian
    public function down(): void
    {
        Schema::dropIfExists('jadwal_bagians');
    }
};
