@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-500 mt-2">Selamat datang di Sistem Manajemen Pelatihan Karyawan</p>
    @if(auth()->user()->role === 'supervisor' && $supervisorBagian)
        <p class="text-blue-600 font-semibold mt-1">
            ℹ️ Anda mengelola Bagian <span class="text-blue-700">{{ $supervisorBagian->nama_bagian }}</span>
        </p>
    @endif
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Jadwal Bulan Ini -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Pelatihan</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalJadwal }}</p>
            </div>
            <svg class="w-12 h-12 text-blue-100" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
        </div>
        <p class="text-xs text-gray-400 mt-2">Bulan ini</p>
    </div>

    <!-- Jadwal Ongoing Hari Ini -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Berlangsung Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $ongoingToday }}</p>
            </div>
            <svg class="w-12 h-12 text-yellow-100" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
        </div>
    </div>

    <!-- Total Peserta -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Peserta</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalPeserta }}</p>
            </div>
            <svg class="w-12 h-12 text-green-100" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
        </div>
        <p class="text-xs text-gray-400 mt-2">Bulan ini</p>
    </div>

    <!-- Persentase Kehadiran -->
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Kehadiran</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $persentaseKehadiran }}%</p>
            </div>
            <svg class="w-12 h-12 text-red-100" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h.01a1 1 0 110 2H12zm-2 2a1 1 0 100-2 1 1 0 000 2zm4 0a1 1 0 100-2 1 1 0 000 2zm2-2a1 1 0 110-2h.01a1 1 0 110 2H16zm-6 4a2 2 0 110-4 2 2 0 010 4zm4 0a2 2 0 110-4 2 2 0 010 4zm2 4a1 1 0 100-2H6a1 1 0 100 2h10z" clip-rule="evenodd"></path></svg>
        </div>
        <p class="text-xs text-gray-400 mt-2">Bulan ini</p>
    </div>
</div>

<!-- Calendar + Event List -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Calendar Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800">{{ $currentMonth }}</h3>
            </div>

            <!-- Calendar Grid -->
            <div class="space-y-4">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 gap-2 mb-2">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                        <div class="text-center text-xs font-bold text-gray-600">{{ $dayName }}</div>
                    @endforeach
                </div>

                <!-- Calendar Days -->
                @foreach($calendarDays as $week)
                    <div class="grid grid-cols-7 gap-2">
                        @foreach($week as $day)
                            @if($day === null)
                                <div class="aspect-square"></div>
                            @else
                                <div class="relative group">
                                    <div class="aspect-square flex items-center justify-center rounded-lg text-sm font-semibold transition-colors
                                        @if($day['hasTraining'])
                                            bg-blue-500 text-white cursor-pointer hover:bg-blue-600
                                        @elseif($day['date']->isToday())
                                            bg-orange-100 text-orange-900 border-2 border-orange-400
                                        @else
                                            bg-gray-100 text-gray-700 hover:bg-gray-200
                                        @endif
                                    ">
                                        {{ $day['day'] }}
                                    </div>

                                    <!-- Popover -->
                                    @if($day['hasTraining'])
                                        <div class="absolute left-0 top-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg p-3 w-48 z-20 hidden group-hover:block">
                                            <h5 class="font-semibold text-gray-800 text-xs mb-2 border-b pb-2">Jadwal Hari Ini</h5>
                                            <div class="space-y-2">
                                                @foreach($day['jadwals'] as $jadwal)
                                                    <div class="text-xs border-l-2 border-blue-500 pl-2">
                                                        <p class="font-semibold text-gray-800">{{ $jadwal->JenisPelatihan->nama_jenis }}</p>
                                                        <p class="text-gray-600">{{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}</p>
                                                        <p class="text-gray-500">📍 {{ $jadwal->tempat }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>

            <!-- Legend -->
            <div class="mt-6 pt-6 border-t border-gray-200 space-y-2">
                <div class="flex items-center text-xs">
                    <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                    <span class="text-gray-700">Ada Pelatihan</span>
                </div>
                <div class="flex items-center text-xs">
                    <div class="w-4 h-4 bg-orange-100 border-2 border-orange-400 rounded mr-2"></div>
                    <span class="text-gray-700">Hari Ini</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Event List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📅 Jadwal Pelatihan - {{ $currentMonth }}</h3>

            @if($jadwalsBulanIni->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($jadwalsBulanIni as $jadwal)
                        @php
                            $today = now()->startOfDay();
                            $jadwalDate = $jadwal->tanggal_pelaksanaan->startOfDay();
                            
                            if ($jadwalDate->isBefore($today)) {
                                $status = 'ENDED';
                                $statusColor = 'bg-gray-100 text-gray-700 border-gray-300';
                                $dateColor = 'text-gray-500';
                            } elseif ($jadwalDate->isToday()) {
                                $status = 'ONGOING';
                                $statusColor = 'bg-orange-100 text-orange-700 border-orange-300';
                                $dateColor = 'text-orange-600 font-semibold';
                            } else {
                                $status = 'UPCOMING';
                                $statusColor = 'bg-blue-100 text-blue-700 border-blue-300';
                                $dateColor = 'text-blue-600 font-semibold';
                            }
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="font-semibold text-gray-800">{{ $jadwal->JenisPelatihan->nama_jenis }}</h4>
                                <span class="text-xs px-2 py-1 rounded border {{ $statusColor }}">
                                    {{ $status }}
                                </span>
                            </div>
                            
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v2H4a2 2 0 00-2 2v2h16V7a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v2H7V3a1 1 0 00-1-1zm0 5a2 2 0 002 2h8a2 2 0 002-2H6z" clip-rule="evenodd"></path></svg>
                                    <span class="{{ $dateColor }}">
                                        {{ $jadwal->tanggal_pelaksanaan->format('d M Y') }} • 
                                        {{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                    {{ $jadwal->tempat }}
                                </div>

                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                                    @php
                                        $bagianCount = $jadwal->JadwalBagian->count();
                                    @endphp
                                    {{ $bagianCount }} Bagian
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></path></svg>
                    <p class="text-gray-500">Tidak ada pelatihan bulan ini</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
