<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelatihan;
use App\Models\Karyawan;
use App\Models\PresensiPelatihan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        
        // Format data untuk JavaScript
        $jadwalsForJs = $jadwals->map(function ($jadwal) {
            $totalKaryawan = $jadwal->total_karyawan ?? 1;
            $hadirCount = $jadwal->hadir_count ?? 0;
            $persentaseHadir = $totalKaryawan > 0 ? round(($hadirCount / $totalKaryawan) * 100) : 0;
            
            // Tentukan status jadwal
            $today = now()->startOfDay();
            $jadwalDate = $jadwal->tanggal_pelaksanaan->startOfDay();
            
            if ($jadwalDate->isBefore($today)) {
                $status = 'ENDED';
            } elseif ($jadwalDate->isToday()) {
                $status = 'ONGOING';
            } else {
                $status = 'UPCOMING';
            }
            
            return [
                'id_jadwal' => $jadwal->id_jadwal,
                'nama_jenis' => $jadwal->JenisPelatihan->nama_jenis,
                'tempat' => $jadwal->tempat,
                'tanggal_pelaksanaan' => $jadwal->tanggal_pelaksanaan->format('d M Y'),
                'jam_mulai' => $jadwal->jam_mulai->format('H:i'),
                'jam_selesai' => $jadwal->jam_selesai->format('H:i'),
                'hadir_count' => $jadwal->hadir_count,
                'belum_absen_count' => $jadwal->belum_absen_count,
                'total_karyawan' => $totalKaryawan,
                'persentase_hadir' => $persentaseHadir,
                'status' => $status,
            ];
        })->values();
        
        return view('admin.kehadiran', compact('jadwals', 'jadwalsForJs'));
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
    
    // Export laporan kehadiran ke PDF atau Excel
    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        
        // Query jadwal dengan filter tanggal jika ada
        $jadwals = JadwalPelatihan::with(['JenisPelatihan', 'JadwalBagian'])
            ->when($dateFrom, function ($query) use ($dateFrom) {
                return $query->where('tanggal_pelaksanaan', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query) use ($dateTo) {
                return $query->where('tanggal_pelaksanaan', '<=', $dateTo);
            })
            ->orderBy('tanggal_pelaksanaan', 'desc')
            ->get()
            ->map(function ($jadwal) {
                // Hitung statistik kehadiran
                $bagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();
                $totalKaryawan = Karyawan::whereIn('id_bagian', $bagianIds)->count();
                
                $hadirCount = PresensiPelatihan::where('id_jadwal', $jadwal->id_jadwal)
                    ->where('status_kehadiran', 'Hadir')
                    ->count();
                
                $totalPresensi = PresensiPelatihan::where('id_jadwal', $jadwal->id_jadwal)
                    ->distinct('id_karyawan')
                    ->count();
                
                $belumAbsenCount = $totalKaryawan - $totalPresensi;
                $persentaseHadir = $totalKaryawan > 0 ? round(($hadirCount / $totalKaryawan) * 100) : 0;
                
                return [
                    'id_jadwal' => $jadwal->id_jadwal,
                    'nama_jenis' => $jadwal->JenisPelatihan->nama_jenis,
                    'tempat' => $jadwal->tempat,
                    'tanggal' => $jadwal->tanggal_pelaksanaan->format('d M Y'),
                    'jam_mulai' => $jadwal->jam_mulai->format('H:i'),
                    'jam_selesai' => $jadwal->jam_selesai->format('H:i'),
                    'total_karyawan' => $totalKaryawan,
                    'hadir' => $hadirCount,
                    'belum_absen' => $belumAbsenCount,
                    'persentase' => $persentaseHadir . '%'
                ];
            });
        
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.kehadiran-pdf', compact('jadwals'))
                ->setPaper('a4', 'landscape');
            
            $filename = 'Laporan_Kehadiran_' . now()->format('d-m-Y_H-i-s') . '.pdf';
            return $pdf->download($filename);
        } else {
            // Excel export - return simple view for now, bisa di-enhance dengan library Excel
            return response()->json(['message' => 'Excel export belum diimplementasikan'], 501);
        }
    }
}
