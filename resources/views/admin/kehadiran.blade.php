@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Kehadiran</h1>
        <p class="text-gray-500 text-sm">Pilih jadwal pelatihan untuk mengelola kehadiran karyawan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($jadwals as $jadwal)
            <a href="{{ route('kehadiran.show', $jadwal->id_jadwal) }}" 
               class="group block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-brand-blue transition-all overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 bg-blue-50 text-brand-blue text-[10px] font-bold rounded-lg uppercase tracking-wider">
                            {{ $jadwal->tanggal_pelaksanaan->format('d M Y') }}
                        </span>
                        <span class="text-xs text-gray-400 font-medium">
                            {{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 group-hover:text-brand-blue transition-colors line-clamp-2">
                        {{ $jadwal->JenisPelatihan->nama_jenis }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-2">Tempat: {{ $jadwal->tempat }}</p>
                </div>
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-xs font-bold text-green-600 uppercase">{{ $jadwal->hadir_count }} Hadir</span>
                    <span class="text-xs font-bold text-gray-400 uppercase">{{ $jadwal->belum_absen_count }} Belum</span>
                </div>
            </a>
        @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500 text-lg">Belum ada jadwal pelatihan</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
