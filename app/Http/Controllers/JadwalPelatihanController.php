<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelatihan;
use App\Models\JenisPelatihan;
use App\Models\Bagian;
use App\Models\Karyawan;
use App\Models\JadwalBagian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalPelatihanController extends Controller
{
    public function index()
    {
        $jadwalPelatihans = JadwalPelatihan::with('JenisPelatihan', 'JadwalBagian')->get();
        return view('admin.penjadwalan', compact('jadwalPelatihans'));
    }

    public function create()
    {
        $jenisPelatihans = JenisPelatihan::all();
        $bagians = Bagian::all();
        return view('admin.penjadwalan-create', compact('jenisPelatihans', 'bagians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jenis' => 'required|exists:jenis_pelatihan,id_jenis',
            'tanggal_pelaksanaan' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tempat' => 'required|string',
            'catatan' => 'nullable|string',
            'bagians' => 'required|array|min:1',
            'bagians.*' => 'exists:bagian,id_bagian',
        ]);
        
        // Auto-calculate tenggat_presensi = H+5 dari tanggal pelaksanaan (23:59:59)
        $tenggat_presensi = Carbon::parse($validated['tanggal_pelaksanaan'])
            ->addDays(5)
            ->endOfDay(); // Set ke 23:59:59

        // Generate link presensi dan QR code
        $link_presensi = 'https://presensi.example.com/' . uniqid();
        $qr_code = 'QR_' . uniqid();

        // Create jadwal pelatihan dengan lokasi otomatis dari konstanta PresensiController
        $jadwal = JadwalPelatihan::create([
            'id_jenis' => $validated['id_jenis'],
            'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
            'jam_mulai' => $validated['tanggal_pelaksanaan'] . ' ' . $validated['jam_mulai'],
            'jam_selesai' => $validated['tanggal_pelaksanaan'] . ' ' . $validated['jam_selesai'],
            'tempat' => $validated['tempat'],
            'tenggat_presensi' => $tenggat_presensi,
            'link_presensi' => null,
            'qr_code' => null,
            'waktu_mulai_presensi' => null,
            'waktu_berakhir_presensi' => null,
            'status' => 'draft',
            'catatan' => $validated['catatan'] ?? null,
            'location_latitude' => -7.048357, 
            'location_longitude' => 110.437872,
            'location_radius' => 700,
            'location_name' => 'Kantor Pusat',
        ]);

        // Assign bagians to jadwal
        foreach ($validated['bagians'] as $id_bagian) {
            JadwalBagian::create([
                'id_jadwal' => $jadwal->id_jadwal,
                'id_bagian' => $id_bagian,
            ]);
        }
        
        return redirect()->route('penjadwalan.index')
            ->with('success', 'Jadwal pelatihan berhasil dibuat. Tenggat presensi: ' . $tenggat_presensi->format('d M Y H:i'));
    }

    public function edit($id)
    {
        $jadwal = JadwalPelatihan::with('JadwalBagian')->findOrFail($id);
        $jenisPelatihans = JenisPelatihan::all();
        $bagians = Bagian::all();
        $selectedBagians = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();

        return view('admin.penjadwalan-edit', compact('jadwal', 'jenisPelatihans', 'bagians', 'selectedBagians'));
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPelatihan::findOrFail($id);

        $validated = $request->validate([
            'id_jenis' => 'required|exists:jenis_pelatihan,id_jenis',
            'tanggal_pelaksanaan' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tempat' => 'required|string',
            'tenggat_presensi' => 'required|date_format:Y-m-d\TH:i',
            'status' => 'required|in:draft,published,selesai',
            'catatan' => 'nullable|string',
            'bagians' => 'required|array|min:1',
            'bagians.*' => 'exists:bagian,id_bagian',
        ]);

        // Update jadwal pelatihan dengan lokasi yang sama (dari konstanta)
        $jadwal->update([
            'id_jenis' => $validated['id_jenis'],
            'tanggal_pelaksanaan' => $validated['tanggal_pelaksanaan'],
            'jam_mulai' => $validated['tanggal_pelaksanaan'] . ' ' . $validated['jam_mulai'],
            'jam_selesai' => $validated['tanggal_pelaksanaan'] . ' ' . $validated['jam_selesai'],
            'tempat' => $validated['tempat'],
            'tenggat_presensi' => $validated['tenggat_presensi'],
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
            // Lokasi tetap sama dari konstanta (tidak ada input dari form)
            'location_latitude' => -7.048357, 
            'location_longitude' => 110.437872,
            'location_radius' => 200,
            'location_name' => 'Kantor Pusat',
        ]);

        // Update bagians
        $jadwal->JadwalBagian()->delete();
        foreach ($validated['bagians'] as $id_bagian) {
            JadwalBagian::create([
                'id_jadwal' => $jadwal->id_jadwal,
                'id_bagian' => $id_bagian,
            ]);
        }

        return redirect()->route('penjadwalan.index')->with('success', 'Jadwal pelatihan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $jadwal = JadwalPelatihan::findOrFail($id);
        $jadwal->JadwalBagian()->delete();
        $jadwal->delete();

        return redirect()->route('penjadwalan.index')->with('success', 'Jadwal pelatihan berhasil dihapus');
    }

    public function show($id)
    {
        $jadwal = JadwalPelatihan::with('JenisPelatihan', 'JadwalBagian', 'PresensiPelatihan')->findOrFail($id);
        $bagians = Bagian::all();
        
        // Get karyawan by bagian
        $karyawanByBagian = [];
        foreach ($jadwal->JadwalBagian as $jadwalBagian) {
            $karyawanByBagian[$jadwalBagian->id_bagian] = Karyawan::where('id_bagian', $jadwalBagian->id_bagian)->get();
        }

        return view('admin.penjadwalan-detail', compact('jadwal', 'bagians', 'karyawanByBagian'));
    }

    // public function generatePresensi($id)
    // {
    //     $jadwal = JadwalPelatihan::findOrFail($id);
        
    //     // Hanya bisa generate jika status draft dan jam sudah mulai
    //     if ($jadwal->status !== 'draft') {
    //         return redirect()->route('penjadwalan.show', $id)->with('error', 'Link presensi hanya bisa di-generate dari status draft');
    //     }

    //     // Generate link dan QR code
    //     $link_presensi = 'http://localhost:8000/presensi/' . $jadwal->id_jadwal . '/' . uniqid();
    //     $qr_code = 'QR_' . uniqid();
        
    //     $jadwal->update([
    //         'link_presensi' => $link_presensi,
    //         'qr_code' => $qr_code,
    //         'waktu_mulai_presensi' => now(),
    //         'waktu_berakhir_presensi' => now()->addMinutes(30),
    //         'status' => 'published',
    //     ]);

    //     return redirect()->route('penjadwalan.show', $id)->with('success', 'Link presensi berhasil di-generate. Aktif selama 30 menit');
    // }

    public function generatePresensi($id)
    {
        $jadwal = JadwalPelatihan::findOrFail($id);
        
        // Validasi: Cek apakah sudah lewat H+5
        if (now() > $jadwal->tenggat_presensi) {
            return redirect()->back()
                ->with('error', 'Periode presensi sudah berakhir (lewat H+5)');
        }

        // Validasi: Cek apakah sudah ada link presensi yang aktif
        if ($jadwal->link_presensi && now() <= $jadwal->waktu_berakhir_presensi) {
            return redirect()->back()
                ->with('error', 'Presensi sudah aktif. Link masih berlaku sampai ' . $jadwal->waktu_berakhir_presensi->format('H:i'));
        }

        // Generate unique token
        $token = bin2hex(random_bytes(16)); // 32 karakter random
        
        // Generate link presensi
        $link_presensi = url("/presensi/{$jadwal->id_jadwal}/{$token}");
        
        // Generate QR code menggunakan online API (tidak perlu GD extension)
        $qr_code = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($link_presensi);
        
        $jadwal->update([
            'link_presensi' => $link_presensi,
            'qr_code' => $qr_code,
            'waktu_mulai_presensi' => now(),
            'waktu_berakhir_presensi' => now()->addMinutes(30), // 30 menit dari sekarang
        ]);

        return redirect()->back()
            ->with('success', 'Link presensi berhasil di-generate. Aktif sampai ' . $jadwal->waktu_berakhir_presensi->format('H:i'));
    }

    // public function extendPresensi($id)
    // {
    //     $jadwal = JadwalPelatihan::findOrFail($id);
        
    //     // Hanya bisa perpanjang jika status published
    //     if ($jadwal->status !== 'published') {
    //         return redirect()->route('penjadwalan.show', $id)->with('error', 'Hanya bisa perpanjang presensi yang sedang aktif');
    //     }

    //     // Cek apakah masih dalam periode h+5
    //     $hariPelatihan = $jadwal->tanggal_pelaksanaan;
    //     $batasAkhir = $hariPelatihan->addDays(5)->endOfDay();
        
    //     if (now() > $batasAkhir) {
    //         $jadwal->update(['status' => 'selesai']);
    //         return redirect()->route('penjadwalan.show', $id)->with('error', 'Periode presensi sudah berakhir (H+5). Status jadwal menjadi SELESAI');
    //     }

    //     // Perpanjang 30 menit
    //     $jadwal->update([
    //         'waktu_berakhir_presensi' => now()->addMinutes(30),
    //     ]);

    //     return redirect()->route('penjadwalan.show', $id)->with('success', 'Presensi berhasil diperpanjang 30 menit');
    // }

    public function extendPresensi($id)
    {
        $jadwal = JadwalPelatihan::findOrFail($id);
        
        // Validasi: Cek apakah masih dalam periode H+5
        if (now() > $jadwal->tenggat_presensi) {
            $jadwal->update(['status' => 'selesai']);
            return redirect()->back()
                ->with('error', 'Periode presensi sudah berakhir (H+5). Status menjadi SELESAI.');
        }

        // Validasi: Hanya bisa perpanjang jika link masih ada
        if (!$jadwal->link_presensi) {
            return redirect()->back()
                ->with('error', 'Tidak ada link presensi yang aktif. Silakan generate ulang.');
        }

        // Perpanjang 30 menit dari SEKARANG (bukan dari waktu berakhir lama)
        $jadwal->update([
            'waktu_berakhir_presensi' => now()->addMinutes(30),
        ]);

        return redirect()->back()
            ->with('success', 'Presensi berhasil diperpanjang 30 menit (sampai ' . now()->addMinutes(30)->format('H:i') . ')');
    }
}