<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelatihan;
use App\Models\Karyawan;
use App\Models\PresensiPelatihan;
use App\Models\Bagian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        
        // Get current month range
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        
        // Base query
        $query = JadwalPelatihan::with(['JenisPelatihan', 'JadwalBagian'])
            ->whereBetween('tanggal_pelaksanaan', [$startOfMonth, $endOfMonth]);
        
        // Filter untuk supervisor
        if ($currentUser->role === 'supervisor') {
            $supervisorBagian = Bagian::where('email', $currentUser->email)->first();
            
            if ($supervisorBagian) {
                $query->whereHas('JadwalBagian', function ($q) use ($supervisorBagian) {
                    $q->where('id_bagian', $supervisorBagian->id_bagian);
                });
            } else {
                // Supervisor tanpa bagian - return empty
                return view('dashboard', [
                    'totalJadwal' => 0,
                    'ongoingToday' => 0,
                    'totalPeserta' => 0,
                    'persentaseKehadiran' => 0,
                    'jadwalsBulanIni' => collect(),
                    'calendarDays' => [],
                    'currentMonth' => $now->format('F Y'),
                    'supervisorBagian' => null,
                ]);
            }
        }
        
        // Get jadwal untuk bulan ini
        $jadwalsBulanIni = $query->orderBy('tanggal_pelaksanaan', 'asc')->get();
        
        // STAT 1: Total Jadwal Bulan Ini
        $totalJadwal = $jadwalsBulanIni->count();
        
        // STAT 2: Jadwal Ongoing Hari Ini
        $today = now()->startOfDay();
        $ongoingToday = $jadwalsBulanIni->filter(function ($jadwal) use ($today) {
            return $jadwal->tanggal_pelaksanaan->startOfDay()->isToday();
        })->count();
        
        // STAT 3: Total Peserta Bulan Ini
        $totalPeserta = 0;
        foreach ($jadwalsBulanIni as $jadwal) {
            $bagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();
            
            if ($currentUser->role === 'supervisor') {
                $supervisorBagian = Bagian::where('email', $currentUser->email)->first();
                $bagianIds = $supervisorBagian ? [$supervisorBagian->id_bagian] : [];
            }
            
            $count = Karyawan::whereIn('id_bagian', $bagianIds)->count();
            $totalPeserta += $count;
        }
        
        // STAT 4: Persentase Kehadiran Bulan Ini
        $totalHadir = 0;
        $totalSeharusnyaHadir = 0;
        
        foreach ($jadwalsBulanIni as $jadwal) {
            $bagianIds = $jadwal->JadwalBagian->pluck('id_bagian')->toArray();
            
            if ($currentUser->role === 'supervisor') {
                $supervisorBagian = Bagian::where('email', $currentUser->email)->first();
                $bagianIds = $supervisorBagian ? [$supervisorBagian->id_bagian] : [];
            }
            
            $karyawanIds = Karyawan::whereIn('id_bagian', $bagianIds)->pluck('id_karyawan')->toArray();
            
            // Total karyawan yang seharusnya hadir (distinct)
            $distinctKaryawanCount = count($karyawanIds);
            $totalSeharusnyaHadir += $distinctKaryawanCount;
            
            // Hitung yang sudah hadir
            $hadirCount = PresensiPelatihan::where('id_jadwal', $jadwal->id_jadwal)
                ->where('status_kehadiran', 'Hadir')
                ->whereIn('id_karyawan', $karyawanIds)
                ->distinct('id_karyawan')
                ->count();
            
            $totalHadir += $hadirCount;
        }
        
        $persentaseKehadiran = $totalSeharusnyaHadir > 0 
            ? round(($totalHadir / $totalSeharusnyaHadir) * 100) 
            : 0;
        
        // Prepare calendar data
        $calendarDays = $this->generateCalendar($now, $jadwalsBulanIni);
        
        // Get supervisor bagian (if supervisor)
        $supervisorBagian = null;
        if ($currentUser->role === 'supervisor') {
            $supervisorBagian = Bagian::where('email', $currentUser->email)->first();
        }
        
        return view('dashboard', [
            'totalJadwal' => $totalJadwal,
            'ongoingToday' => $ongoingToday,
            'totalPeserta' => $totalPeserta,
            'persentaseKehadiran' => $persentaseKehadiran,
            'jadwalsBulanIni' => $jadwalsBulanIni,
            'calendarDays' => $calendarDays,
            'currentMonth' => $now->format('F Y'),
            'supervisorBagian' => $supervisorBagian,
            'now' => $now,
        ]);
    }
    
    private function generateCalendar($month, $jadwals)
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        
        // Group jadwals by tanggal
        $jadwalsByDate = $jadwals->groupBy(function ($jadwal) {
            return $jadwal->tanggal_pelaksanaan->format('Y-m-d');
        });
        
        $days = [];
        $currentDate = $startOfMonth->copy();
        
        // Add empty days for days before month starts
        $dayOfWeek = $currentDate->dayOfWeek; // 0 = Sunday
        for ($i = 0; $i < $dayOfWeek; $i++) {
            $days[] = null;
        }
        
        // Add all days of month
        while ($currentDate <= $endOfMonth) {
            $dateKey = $currentDate->format('Y-m-d');
            $days[] = [
                'date' => $currentDate->copy(),
                'day' => $currentDate->day,
                'hasTraining' => isset($jadwalsByDate[$dateKey]),
                'jadwals' => $jadwalsByDate[$dateKey] ?? collect(),
            ];
            $currentDate->addDay();
        }
        
        return array_chunk($days, 7);
    }
}
