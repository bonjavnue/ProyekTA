<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelatihan;
use App\Models\PresensiPelatihan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function getJadwal($id_jadwal)
    {
        try {
            $jadwal = JadwalPelatihan::with('JenisPelatihan')->findOrFail($id_jadwal);
            
            // Cek apakah presensi masih aktif
            if ($jadwal->status !== 'published') {
                return response()->json([
                    'success' => false,
                    'message' => 'Link presensi belum aktif'
                ], 403);
            }
            
            if (!$jadwal->waktu_berakhir_presensi || now() > $jadwal->waktu_berakhir_presensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link presensi sudah berakhir'
                ], 403);
            }

            // Hitung sisa waktu dalam menit
            $sisaMenit = now()->diffInMinutes($jadwal->waktu_berakhir_presensi, false);
            
            // Pastikan format datetime untuk tanggal dan jam
            $tanggal = is_string($jadwal->tanggal_pelaksanaan) 
                ? \Carbon\Carbon::parse($jadwal->tanggal_pelaksanaan)->format('d M Y')
                : $jadwal->tanggal_pelaksanaan->format('d M Y');
                
            $jamMulai = is_string($jadwal->jam_mulai)
                ? substr($jadwal->jam_mulai, 0, 5)
                : (is_object($jadwal->jam_mulai) ? $jadwal->jam_mulai->format('H:i') : substr((string)$jadwal->jam_mulai, 0, 5));
                
            $jamSelesai = is_string($jadwal->jam_selesai)
                ? substr($jadwal->jam_selesai, 0, 5)
                : (is_object($jadwal->jam_selesai) ? $jadwal->jam_selesai->format('H:i') : substr((string)$jadwal->jam_selesai, 0, 5));

            return response()->json([
                'success' => true,
                'jadwal' => [
                    'id_jadwal' => $jadwal->id_jadwal,
                    'nama_pelatihan' => $jadwal->JenisPelatihan->nama_jenis ?? 'Pelatihan',
                    'tanggal' => $tanggal,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'tempat' => $jadwal->tempat,
                    'sisa_waktu' => max(0, $sisaMenit) . ' menit',
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitPresensi(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_jadwal' => 'required|integer|exists:jadwal_pelatihans,id_jadwal',
                'token' => 'required|string',
                'id_karyawan' => 'required|string',
                'nik' => 'required|string',
                'nama' => 'required|string',
            ]);

            $jadwal = JadwalPelatihan::findOrFail($validated['id_jadwal']);

            // Cek apakah presensi masih aktif
            if ($jadwal->status !== 'published' || now() > $jadwal->waktu_berakhir_presensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Link presensi tidak aktif atau sudah berakhir'
                ], 403);
            }

            // Cek apakah sudah absen
            $existingPresensi = PresensiPelatihan::where('id_jadwal', $validated['id_jadwal'])
                ->where('id_karyawan', $validated['id_karyawan'])
                ->first();

            if ($existingPresensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi untuk jadwal ini'
                ], 400);
            }

            // Buat presensi baru
            PresensiPelatihan::create([
                'id_jadwal' => $validated['id_jadwal'],
                'id_karyawan' => $validated['id_karyawan'],
                'waktu_presensi' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil dicatat',
                'data' => [
                    'nama' => $validated['nama'],
                    'waktu' => now()->format('d M Y H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
