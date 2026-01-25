<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== USERS DATA ===\n";
$users = DB::table('users')->select('email', 'role')->get();
foreach ($users as $user) {
    echo "Email: {$user->email}, Role: {$user->role}\n";
}

echo "\n=== BAGIAN DATA ===\n";
$bagians = DB::table('bagian')->get();
foreach ($bagians as $b) {
    echo "ID: {$b->id_bagian}, Nama: {$b->nama_bagian}, Email: {$b->email}\n";
}

echo "\n=== KARYAWAN COUNT PER BAGIAN ===\n";
$karyawanCount = DB::table('karyawans')->select('id_bagian', DB::raw('count(*) as count'))->groupBy('id_bagian')->get();
foreach ($karyawanCount as $k) {
    echo "Bagian ID: {$k->id_bagian}, Total: {$k->count}\n";
}

echo "\n=== JADWAL DATA ===\n";
$jadwals = DB::table('jadwal_pelatihans')->get(['id_jadwal']);
echo "Total Jadwal: " . count($jadwals) . "\n";

echo "\n=== JADWAL_BAGIAN DATA ===\n";
$jadwalBagians = DB::table('jadwal_bagians')->select('id_jadwal', 'id_bagian')->get();
foreach ($jadwalBagians as $jb) {
    echo "Jadwal ID: {$jb->id_jadwal}, Bagian ID: {$jb->id_bagian}\n";
}
