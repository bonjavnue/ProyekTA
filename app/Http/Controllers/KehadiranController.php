<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelatihan;
use App\Models\Karyawan;
use App\Models\PresensiPelatihan;
use Illuminate\Http\Request;

class KehadiranController extends Controller
{
    // List semua jadwal dengan summary kehadiran
    public function index()
    {
        $jadwals = JadwalPelatihan::with(['JenisPelatihan', 'JadwalBagian'])
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->get()
            ->map(function ($jadwal) {
                // Hitung total karyawan seharusnya hadir
                $bagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();
                $totalKaryawan = Karyawan::whereIn('id_bagian', $bagianIds)->count();
                
                // Hitung yang sudah hadir
                $hadirCount = PresensiPelatihan::where('id_jadwal', $jadwal->id_jadwal)
                    ->where('status_kehadiran', 'Hadir')
                    ->count();
                
                // Hitung total yang punya record presensi
                $totalPresensi = PresensiPelatihan::where('id_jadwal', $jadwal->id_jadwal)
                    ->distinct('id_karyawan')
                    ->count();
                
                // Yang belum presensi
                $belumAbsenCount = $totalKaryawan - $totalPresensi;
                
                $jadwal->total_karyawan = $totalKaryawan;
                $jadwal->hadir_count = $hadirCount;
                $jadwal->belum_absen_count = $belumAbsenCount;
                
                return $jadwal;
            });
        
        return view('admin.kehadiran', compact('jadwals'));
    }
    
    // Detail jadwal + semua karyawan + status mereka
    public function show($id)
    {
        $jadwal = JadwalPelatihan::with(['JenisPelatihan', 'JadwalBagian'])
            ->findOrFail($id);
        
        // Ambil bagian yang terdaftar di jadwal ini
        $bagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();
        
        // Ambil semua karyawan dari bagian tersebut
        $karyawans = Karyawan::whereIn('id_bagian', $bagianIds)
            ->with(['Bagian'])
            ->orderBy('nama_karyawan')
            ->get()
            ->map(function ($karyawan) use ($id) {
                // Cek apakah ada presensi
                $presensi = PresensiPelatihan::where('id_jadwal', $id)
                    ->where('id_karyawan', $karyawan->id_karyawan)
                    ->first();
                
                if (!$presensi) {
                    // Belum presensi
                    $karyawan->status = 'Belum Presensi';
                    $karyawan->waktu_presensi = null;
                    $karyawan->dicatat_oleh = null;
                } else {
                    // Sudah presensi/admin ubah status
                    $karyawan->status = $presensi->status_kehadiran;
                    $karyawan->waktu_presensi = $presensi->waktu_presensi;
                    $karyawan->dicatat_oleh = $presensi->dicatat_oleh;
                }
                
                return $karyawan;
            });
        
        return view('admin.kehadiran-detail', compact('jadwal', 'karyawans'));
    }
    
    // Update status kehadiran (admin/supervisor mengubah status)
    public function updateStatus(Request $request, $id, $id_karyawan)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:Hadir,Sakit,Izin,Alpa,Belum Presensi',
            ]);
            
            // Tentukan siapa yang mencatat berdasarkan role user
            $currentUser = auth()->user();
            $dicatatOleh = $currentUser->role ?? 'admin'; // Ambil role dari current user
            
            $presensi = PresensiPelatihan::where('id_jadwal', $id)
                ->where('id_karyawan', $id_karyawan)
                ->first();
            
            if (!$presensi) {
                // Jika belum ada, buat baru (admin/supervisor absenkan)
                if ($validated['status'] !== 'Belum Presensi') {
                    $presensi = PresensiPelatihan::create([
                        'id_jadwal' => $id,
                        'id_karyawan' => $id_karyawan,
                        'status_kehadiran' => $validated['status'],
                        'waktu_presensi' => now(),
                        'dicatat_oleh' => $dicatatOleh,
                    ]);
                }
            } else {
                // Update existing
                if ($validated['status'] === 'Belum Presensi') {
                    // Jika di-set ke Belum Presensi, hapus record
                    $presensi->delete();
                    $presensi = null;
                } else {
                    // Update status dan tandai dicatat oleh admin/supervisor
                    $presensi->update([
                        'status_kehadiran' => $validated['status'],
                        'dicatat_oleh' => $dicatatOleh,
                    ]);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran berhasil diperbarui',
                'dicatat_oleh' => $dicatatOleh,
                'waktu_presensi' => $presensi ? $presensi->waktu_presensi : null
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi error: ' . implode(', ', $e->errors()['status'] ?? [])
            ], 422);
        } catch (\Exception $e) {
            \Log::error('KehadiranController updateStatus error: ' . $e->getMessage(), [
                'jadwal_id' => $id,
                'karyawan_id' => $id_karyawan,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
