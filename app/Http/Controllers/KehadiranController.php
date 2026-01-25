<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelatihan;
use App\Models\Karyawan;
use App\Models\PresensiPelatihan;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KehadiranController extends Controller
{
    // List semua jadwal dengan summary kehadiran
    public function index(Request $request)
    {
        $perPage = request('per_page', 6);
        
        // Validasi per_page - hanya terima nilai yang diizinkan
        $allowedPerPage = [6, 9, 18, 24, 30];
        
        // Handle 'all' option
        if ($perPage === 'all') {
            $perPage = PHP_INT_MAX;
        } elseif (!in_array($perPage, $allowedPerPage)) {
            $perPage = 6;
        }
        
        $query = JadwalPelatihan::with(['JenisPelatihan', 'JadwalBagian'])
            ->orderBy('tanggal_pelaksanaan', 'desc');
        
        // Filter untuk supervisor - hanya jadwal yang melibatkan bagian supervisor
        $currentUser = auth()->user();
        if ($currentUser->role === 'supervisor') {
            // Ambil bagian yang terikat ke supervisor ini
            $supervisorBagian = Bagian::where('email', $currentUser->email)->first();
            
            if ($supervisorBagian) {
                // Filter jadwal yang melibatkan bagian ini
                $query->whereHas('JadwalBagian', function ($q) use ($supervisorBagian) {
                    $q->where('id_bagian', $supervisorBagian->id_bagian);
                });
            } else {
                // Jika supervisor tidak punya bagian, tampilkan empty
                $query->where('id_jadwal', null); // Return empty result
            }
        }
        
        // Paginate results
        $jadwals = $query->paginate($perPage)->appends(request()->query());
        
        // Map data untuk kehadiran info
        $jadwalsMapped = $jadwals->getCollection()->map(function ($jadwal) use ($currentUser) {
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
        $jadwalsForJs = $jadwalsMapped->map(function ($jadwal) {
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
        
        return view('admin.kehadiran', compact('jadwals', 'jadwalsForJs', 'perPage'));
    }
    
    // Detail jadwal + semua karyawan + status mereka
    public function show(Request $request, $id)
    {
        $jadwal = JadwalPelatihan::with(['JenisPelatihan', 'JadwalBagian'])
            ->findOrFail($id);
        
        $currentUser = auth()->user();
        
        // Ambil bagian yang terdaftar di jadwal ini
        $bagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();
        
        // Jika supervisor, filter hanya karyawan dari bagian supervisor
        if ($currentUser->role === 'supervisor') {
            $supervisorBagian = Bagian::where('email', $currentUser->email)->first();
            
            if (!$supervisorBagian || !in_array($supervisorBagian->id_bagian, $bagianIds)) {
                // Supervisor tidak punya akses ke jadwal ini karena tidak sesuai dengan bagiannya
                abort(403, 'Anda tidak memiliki akses ke jadwal ini');
            }
            
            // Filter bagian ids hanya untuk bagian supervisor
            $bagianIds = [$supervisorBagian->id_bagian];
        }
        
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
                
                // Add bagian_nama
                $karyawan->bagian_nama = $karyawan->Bagian->nama_bagian ?? '-';
                
                return $karyawan;
            });
        
        $perPage = request('per_page', 10);
        
        return view('admin.kehadiran-detail', compact('jadwal', 'karyawans', 'perPage'));
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
        
        $currentUser = auth()->user();
        
        // Query jadwal dengan filter tanggal jika ada
        $query = JadwalPelatihan::with(['JenisPelatihan', 'JadwalBagian'])
            ->when($dateFrom, function ($q) use ($dateFrom) {
                return $q->where('tanggal_pelaksanaan', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                return $q->where('tanggal_pelaksanaan', '<=', $dateTo);
            });
        
        // Filter untuk supervisor
        if ($currentUser->role === 'supervisor') {
            $supervisorBagian = Bagian::where('email', $currentUser->email)->first();
            
            if ($supervisorBagian) {
                $query->whereHas('JadwalBagian', function ($q) use ($supervisorBagian) {
                    $q->where('id_bagian', $supervisorBagian->id_bagian);
                });
            } else {
                // Supervisor tanpa bagian tidak bisa export
                abort(403, 'Anda tidak memiliki bagian yang terikat');
            }
        }
        
        $jadwals = $query->orderBy('tanggal_pelaksanaan', 'desc')->get()
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

    public function exportPdf($id)
    {
        $jadwal = JadwalPelatihan::with(['JenisPelatihan', 'JadwalBagian'])
            ->findOrFail($id);
        
        $currentUser = auth()->user();
        
        // Ambil bagian yang terdaftar di jadwal ini
        $bagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();
        
        // Jika supervisor, validasi akses
        if ($currentUser->role === 'supervisor') {
            $supervisorBagian = Bagian::where('email', $currentUser->email)->first();
            
            if (!$supervisorBagian || !in_array($supervisorBagian->id_bagian, $bagianIds)) {
                abort(403, 'Anda tidak memiliki akses untuk export jadwal ini');
            }
        }
        
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
                    return [
                        'id_karyawan' => $karyawan->id_karyawan,
                        'nama_karyawan' => $karyawan->nama_karyawan,
                        'nik' => $karyawan->nik,
                        'bagian' => $karyawan->Bagian->nama_bagian ?? '-',
                        'status' => 'Belum Presensi',
                        'waktu_presensi' => null,
                        'dicatat_oleh' => null,
                    ];
                } else {
                    return [
                        'id_karyawan' => $karyawan->id_karyawan,
                        'nama_karyawan' => $karyawan->nama_karyawan,
                        'nik' => $karyawan->nik,
                        'bagian' => $karyawan->Bagian->nama_bagian ?? '-',
                        'status' => $presensi->status_kehadiran,
                        'waktu_presensi' => $presensi->waktu_presensi,
                        'dicatat_oleh' => $presensi->dicatat_oleh,
                    ];
                }
            });

        // Generate PDF
        $pdf = \PDF::loadView('admin.kehadiran-export-pdf', [
            'jadwal' => $jadwal,
            'karyawans' => $karyawans
        ]);

        return $pdf->download('Laporan_Kehadiran_' . $jadwal->id_jadwal . '_' . now()->format('d-m-Y') . '.pdf');
    }

    // Export kehadiran ke Excel (CSV atau XLS format)
    public function exportExcel(Request $request, $id)
    {
        $format = $request->get('format', 'csv'); // Default: csv
        
        if (!in_array($format, ['csv', 'xls'])) {
            $format = 'csv';
        }
        
        $jadwal = JadwalPelatihan::with(['JenisPelatihan', 'JadwalBagian'])
            ->findOrFail($id);
        
        $currentUser = auth()->user();
        
        // Ambil bagian yang terdaftar di jadwal ini
        $bagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();
        
        // Jika supervisor, validasi akses dan filter hanya bagian supervisor
        if ($currentUser->role === 'supervisor') {
            $supervisorBagian = Bagian::where('email', $currentUser->email)->first();
            
            if (!$supervisorBagian || !in_array($supervisorBagian->id_bagian, $bagianIds)) {
                abort(403, 'Anda tidak memiliki akses untuk export jadwal ini');
            }
            
            // Filter hanya bagian supervisor
            $bagianIds = [$supervisorBagian->id_bagian];
        }
        
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
                    return [
                        'id_karyawan' => $karyawan->id_karyawan,
                        'nama_karyawan' => $karyawan->nama_karyawan,
                        'nik' => $karyawan->nik,
                        'bagian' => $karyawan->Bagian->nama_bagian ?? '-',
                        'status' => 'Belum Presensi',
                        'waktu_presensi' => null,
                        'dicatat_oleh' => null,
                    ];
                } else {
                    return [
                        'id_karyawan' => $karyawan->id_karyawan,
                        'nama_karyawan' => $karyawan->nama_karyawan,
                        'nik' => $karyawan->nik,
                        'bagian' => $karyawan->Bagian->nama_bagian ?? '-',
                        'status' => $presensi->status_kehadiran,
                        'waktu_presensi' => $presensi->waktu_presensi ? $presensi->waktu_presensi->format('d/m/Y H:i:s') : null,
                        'dicatat_oleh' => $presensi->dicatat_oleh,
                    ];
                }
            });

        // Hitung statistik
        $hadir = 0;
        $sakit = 0;
        $izin = 0;
        $alpa = 0;
        $belum = 0;

        foreach ($karyawans as $karyawan) {
            if ($karyawan['status'] === 'Hadir') $hadir++;
            elseif ($karyawan['status'] === 'Sakit') $sakit++;
            elseif ($karyawan['status'] === 'Izin') $izin++;
            elseif ($karyawan['status'] === 'Alpa') $alpa++;
            else $belum++;
        }

        // Siapkan CSV content
        $csv = "LAPORAN KEHADIRAN PELATIHAN\n\n";
        $csv .= "Jenis Pelatihan," . $jadwal->JenisPelatihan->nama_jenis . "\n";
        $csv .= "Tanggal Pelaksanaan," . $jadwal->tanggal_pelaksanaan->format('d M Y') . "\n";
        $csv .= "Waktu Pelatihan," . $jadwal->jam_mulai->format('H:i') . ' - ' . $jadwal->jam_selesai->format('H:i') . "\n";
        $csv .= "Tempat Pelaksanaan," . $jadwal->tempat . "\n";
        $csv .= "Total Peserta," . count($karyawans) . "\n";
        $csv .= "Tanggal Laporan," . now()->format('d M Y H:i') . "\n\n";

        // Header tabel
        $csv .= "No.,Nama Karyawan,NIK,Bagian,Status,Waktu Presensi,Dicatat Oleh\n";

        // Data karyawan
        $no = 1;
        foreach ($karyawans as $karyawan) {
            $csv .= $no++ . ",";
            $csv .= "\"" . addslashes($karyawan['nama_karyawan']) . "\",";
            $csv .= $karyawan['nik'] . ",";
            $csv .= "\"" . addslashes($karyawan['bagian']) . "\",";
            $csv .= $karyawan['status'] . ",";
            $csv .= ($karyawan['waktu_presensi'] ?? '-') . ",";
            $csv .= ($karyawan['dicatat_oleh'] ?? '-') . "\n";
        }

        // Summary
        $csv .= "\nRINGKASAN KEHADIRAN\n";
        $csv .= "Hadir,Sakit,Izin,Alpa,Belum Presensi\n";
        $csv .= "$hadir,$sakit,$izin,$alpa,$belum\n";

        // Tentukan format dan filename
        if ($format === 'xls') {
            // Format XLS (Tab-separated values)
            $xls = str_replace(",", "\t", $csv);
            $filename = 'Laporan_Kehadiran_' . $jadwal->id_jadwal . '_' . now()->format('d-m-Y') . '.xls';
            $contentType = 'application/vnd.ms-excel; charset=utf-8';
            $content = $xls;
        } else {
            // Format CSV
            $filename = 'Laporan_Kehadiran_' . $jadwal->id_jadwal . '_' . now()->format('d-m-Y') . '.csv';
            $contentType = 'text/csv; charset=utf-8';
            $content = $csv;
        }
        
        // Download file
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
