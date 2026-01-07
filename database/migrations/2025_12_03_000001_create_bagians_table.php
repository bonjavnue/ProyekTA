<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Membuat tabel bagian dengan primary key custom
    public function up(): void
    {
        Schema::create('bagian', function (Blueprint $table) {
            $table->integer('id_bagian')->primary();
            $table->string('nama_bagian');
            $table->string('email')->unique();
            $table->foreign('email')->references('email')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    // Menghapus tabel bagian
    public function down(): void
    {
        Schema::dropIfExists('bagian');
    }
};
