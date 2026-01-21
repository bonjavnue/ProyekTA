<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelatihan;
use App\Models\PresensiPelatihan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    // ========== LOKASI KANTOR YANG FIXED ==========
    private const OFFICE_LATITUDE = -7.048357;
    private const OFFICE_LONGITUDE = 110.437872;
    private const OFFICE_RADIUS = 700; // meter
    private const OFFICE_NAME = "Kantor Pusat";
    // =============================================
    // public function getJadwal($id_jadwal)
    // {
    //     try {
    //         $jadwal = JadwalPelatihan::with('JenisPelatihan')->findOrFail($id_jadwal);
            
    //         // Cek apakah presensi masih aktif
    //         if ($jadwal->status !== 'published') {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Link presensi belum aktif'
    //             ], 403);
    //         }
            
    //         if (!$jadwal->waktu_berakhir_presensi || now() > $jadwal->waktu_berakhir_presensi) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Link presensi sudah berakhir'
    //             ], 403);
    //         }

    //         // Hitung sisa waktu dalam menit
    //         $sisaMenit = now()->diffInMinutes($jadwal->waktu_berakhir_presensi, false);
            
    //         // Pastikan format datetime untuk tanggal dan jam
    //         $tanggal = is_string($jadwal->tanggal_pelaksanaan) 
    //             ? \Carbon\Carbon::parse($jadwal->tanggal_pelaksanaan)->format('d M Y')
    //             : $jadwal->tanggal_pelaksanaan->format('d M Y');
                
    //         $jamMulai = is_string($jadwal->jam_mulai)
    //             ? substr($jadwal->jam_mulai, 0, 5)
    //             : (is_object($jadwal->jam_mulai) ? $jadwal->jam_mulai->format('H:i') : substr((string)$jadwal->jam_mulai, 0, 5));
                
    //         $jamSelesai = is_string($jadwal->jam_selesai)
    //             ? substr($jadwal->jam_selesai, 0, 5)
    //             : (is_object($jadwal->jam_selesai) ? $jadwal->jam_selesai->format('H:i') : substr((string)$jadwal->jam_selesai, 0, 5));

    //         return response()->json([
    //             'success' => true,
    //             'jadwal' => [
    //                 'id_jadwal' => $jadwal->id_jadwal,
    //                 'nama_pelatihan' => $jadwal->JenisPelatihan->nama_jenis ?? 'Pelatihan',
    //                 'tanggal' => $tanggal,
    //                 'jam_mulai' => $jamMulai,
    //                 'jam_selesai' => $jamSelesai,
    //                 'tempat' => $jadwal->tempat,
    //                 'sisa_waktu' => max(0, $sisaMenit) . ' menit',
    //             ]
    //         ]);
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Jadwal tidak ditemukan'
    //         ], 404);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function submitPresensi(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'id_jadwal' => 'required|integer|exists:jadwal_pelatihans,id_jadwal',
    //             'token' => 'required|string',
    //             'id_karyawan' => 'required|string',
    //             'nik' => 'required|string',
    //             'nama' => 'required|string',
    //         ]);

    //         $jadwal = JadwalPelatihan::findOrFail($validated['id_jadwal']);

    //         // Cek apakah presensi masih aktif
    //         if ($jadwal->status !== 'published' || now() > $jadwal->waktu_berakhir_presensi) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Link presensi tidak aktif atau sudah berakhir'
    //             ], 403);
    //         }

    //         // Cek apakah sudah absen
    //         $existingPresensi = PresensiPelatihan::where('id_jadwal', $validated['id_jadwal'])
    //             ->where('id_karyawan', $validated['id_karyawan'])
    //             ->first();

    //         if ($existingPresensi) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Anda sudah melakukan presensi untuk jadwal ini'
    //             ], 400);
    //         }

    //         // Buat presensi baru
    //         PresensiPelatihan::create([
    //             'id_jadwal' => $validated['id_jadwal'],
    //             'id_karyawan' => $validated['id_karyawan'],
    //             'waktu_presensi' => now(),
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Presensi berhasil dicatat',
    //             'data' => [
    //                 'nama' => $validated['nama'],
    //                 'waktu' => now()->format('d M Y H:i:s')
    //             ]
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // app/Http/Controllers/PresensiController.php

    public function getJadwal($id)
    {
        $jadwal = JadwalPelatihan::with('JenisPelatihan')->find($id);
        
        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ], 404);
        }

        // Check status
        if ($jadwal->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Link presensi tidak aktif. Silakan hubungi admin.'
            ], 403);
        }

        // Hitung sisa waktu
        $sisaWaktu = now()->diffInMinutes($jadwal->waktu_berakhir_presensi);

        return response()->json([
            'success' => true,
            'jadwal' => [
                'id_jadwal' => $jadwal->id_jadwal,
                'nama_pelatihan' => $jadwal->JenisPelatihan->nama_jenis,
                'tanggal' => $jadwal->tanggal_pelaksanaan->format('d M Y'),
                'jam_mulai' => $jadwal->jam_mulai->format('H:i'),
                'jam_selesai' => $jadwal->jam_selesai->format('H:i'),
                'tempat' => $jadwal->tempat,
                'sisa_waktu' => $sisaWaktu . ' menit',
                'location_latitude' => self::OFFICE_LATITUDE,
                'location_longitude' => self::OFFICE_LONGITUDE,
                'location_radius' => self::OFFICE_RADIUS,
                'location_name' => self::OFFICE_NAME,
            ]
        ]);
    }

    public function submitPresensi(Request $request)
    {
        $validated = $request->validate([
            'id_jadwal' => 'required|exists:jadwal_pelatihans,id_jadwal',
            'token' => 'required|string',
            'id_karyawan' => 'required|string',
            'nik' => 'required|string',
            'nama' => 'required|string',
            'user_latitude' => 'required|numeric|between:-90,90',
            'user_longitude' => 'required|numeric|between:-180,180',
        ]);

        $jadwal = JadwalPelatihan::find($validated['id_jadwal']);

        // Validasi token
        if (!str_contains($jadwal->link_presensi, $validated['token'])) {
            return response()->json([
                'success' => false,
                'message' => 'Token presensi tidak valid'
            ], 403);
        }

        // Validasi status
        if ($jadwal->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Link presensi sudah tidak aktif'
            ], 403);
        }

        // Check karyawan exists dan dari bagian yang benar
        $karyawan = Karyawan::where('id_karyawan', $validated['id_karyawan'])
            ->where('nik', $validated['nik'])
            ->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan atau tidak sesuai'
            ], 404);
        }

        // Check apakah karyawan dari bagian yang di-assign
        $bagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();
        if (!in_array($karyawan->id_bagian, $bagianIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar dalam pelatihan ini'
            ], 403);
        }

        // Check duplicate presensi
        $sudahAbsen = PresensiPelatihan::where('id_jadwal', $jadwal->id_jadwal)
            ->where('id_karyawan', $karyawan->id_karyawan)
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan presensi sebelumnya'
            ], 400);
        }

        // Validasi lokasi jika jadwal punya lokasi
        if (self::OFFICE_LATITUDE && self::OFFICE_LONGITUDE) {
            $distance = PresensiPelatihan::calculateDistance(
                $validated['user_latitude'],
                $validated['user_longitude'],
                self::OFFICE_LATITUDE,
                self::OFFICE_LONGITUDE
            );

            $maxDistance = self::OFFICE_RADIUS;

            if ($distance > $maxDistance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda berada di luar area ' . self::OFFICE_NAME . '. Jarak: ' . round($distance) . 'm dari lokasi (Max: ' . $maxDistance . 'm)',
                    'distance' => round($distance),
                    'max_distance' => $maxDistance
                ], 403);
            }
        }

        // Simpan presensi
        PresensiPelatihan::create([
            'id_jadwal' => $jadwal->id_jadwal,
            'id_karyawan' => $karyawan->id_karyawan,
            'status_kehadiran' => 'Hadir',
            'waktu_presensi' => now(),
            'bukti_kehadiran' => null,
            'dicatat_oleh' => 'karyawan',  // Dicatat oleh karyawan
            'user_latitude' => $validated['user_latitude'],
            'user_longitude' => $validated['user_longitude'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Presensi berhasil dicatat'
        ]);
    }
}
