<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisPelatihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\JenisPelatihan::create([
            'id_jenis' => 'safety-induction',
            'nama_jenis' => 'Safety Induction',
            'deskripsi' => 'Pelatihan keselamatan kerja dasar untuk semua karyawan baru',
        ]);

        \App\Models\JenisPelatihan::create([
            'id_jenis' => 'operator-sewing',
            'nama_jenis' => 'Operator Sewing',
            'deskripsi' => 'Pelatihan operasional mesin jahit untuk bagian produksi',
        ]);

        \App\Models\JenisPelatihan::create([
            'id_jenis' => 'quality-control',
            'nama_jenis' => 'Quality Control',
            'deskripsi' => 'Pelatihan kontrol kualitas produk',
        ]);

        \App\Models\JenisPelatihan::create([
            'id_jenis' => 'leadership',
            'nama_jenis' => 'Leadership',
            'deskripsi' => 'Pelatihan kepemimpinan untuk supervisor dan manager',
        ]);

        \App\Models\JenisPelatihan::create([
            'id_jenis' => 'customer-service',
            'nama_jenis' => 'Customer Service',
            'deskripsi' => 'Pelatihan layanan pelanggan yang baik',
        ]);
    }
}
