<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bagian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test Users FIRST (before Bagian, karena Bagian punya FK ke Users)
        User::create([
            'email' => 'admin@perusahaan.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'email' => 'supervisor@perusahaan.com',
            'password' => Hash::make('password123'),
            'role' => 'supervisor',
        ]);

        // THEN Create test Bagian
        Bagian::create([
            'id_bagian' => 1,
            'nama_bagian' => 'IT & Sistem Informasi',
            'email' => 'admin@perusahaan.com',
        ]);

        Bagian::create([
            'id_bagian' => 2,
            'nama_bagian' => 'Operasional',
            'email' => 'supervisor@perusahaan.com',
        ]);
    }
}
