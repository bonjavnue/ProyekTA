@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    
    <div class="mb-6">
        <a href="{{ route('penjadwalan.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="text-green-700">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">✕</button>
        </div>
    @endif

    <!-- Informasi Jadwal -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-brand-blue to-blue-800 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">{{ $jadwal->JenisPelatihan->nama_jenis ?? '-' }}</h1>
            <p class="text-blue-100 text-sm mt-1">Detail Jadwal Pelatihan</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tanggal Pelaksanaan</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $jadwal->tanggal_pelaksanaan->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Waktu</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tempat</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $jadwal->tempat }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tenggat Presensi</p>
                    <p class="text-base font-semibold text-gray-900">{{ $jadwal->tenggat_presensi->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Status</p>
                    @php
                        $statusColor = match($jadwal->status) {
                            'draft' => 'bg-yellow-100 text-yellow-700',
                            'published' => 'bg-blue-100 text-blue-700',
                            'selesai' => 'bg-green-100 text-green-700',
                            default => 'bg-gray-100 text-gray-700'
                        };
                    @endphp
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                        {{ ucfirst($jadwal->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Link Presensi</p>
                    @if($jadwal->link_presensi)
                        <a href="{{ $jadwal->link_presensi }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Buka Link</a>
                    @else
                        <span class="text-gray-500 text-sm">Belum di-generate</span>
                    @endif
                </div>
            </div>

            @if($jadwal->status !== 'selesai')
                <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        <div>
                            @if($jadwal->status === 'draft')
                                <p class="text-sm text-yellow-800 font-medium">Belum ada link presensi</p>
                                <p class="text-sm text-yellow-700 mt-1">Klik tombol "Generate Presensi" untuk membuat link dan QR code. Link akan aktif selama 30 menit.</p>
                            @elseif($jadwal->status === 'published')
                                <p class="text-sm text-yellow-800 font-medium">Presensi sedang aktif</p>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Waktu aktif: {{ $jadwal->waktu_mulai_presensi->format('H:i') }} - {{ $jadwal->waktu_berakhir_presensi->format('H:i') }}
                                    (Berakhir dalam {{ $jadwal->waktu_berakhir_presensi->diffInMinutes(now()) }} menit)
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="border-t border-gray-200 pt-6">
                <p class="text-sm text-gray-600 mb-2">QR Code</p>
                <p class="text-base font-mono text-gray-900">{{ $jadwal->qr_code }}</p>
            </div>

            @if($jadwal->catatan)
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <p class="text-sm text-gray-600 mb-2">Catatan</p>
                    <p class="text-base text-gray-900">{{ $jadwal->catatan }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bagian dan Peserta -->
    <div class="space-y-6">
        @forelse($jadwal->JadwalBagian as $jadwalBagian)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-blue-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-bold text-gray-900">{{ $jadwalBagian->Bagian->nama_bagian }}</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">No.</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">ID Karyawan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Nama Karyawan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Presensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($karyawanByBagian[$jadwalBagian->id_bagian] as $key => $karyawan)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 text-sm text-gray-900">{{ $key + 1 }}</td>
                                    <td class="px-6 py-3 text-sm font-mono text-gray-700">{{ $karyawan->id_karyawan }}</td>
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $karyawan->nama_karyawan }}</td>
                                    <td class="px-6 py-3 text-sm">
                                        <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-700 rounded font-medium">
                                            {{ $karyawan->status_karyawan ?? 'Aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-center text-sm">
                                        @php
                                            $presensi = $jadwal->PresensiPelatihan->firstWhere('id_karyawan', $karyawan->id_karyawan);
                                        @endphp
                                        @if($presensi)
                                            <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-700 rounded font-medium">
                                                Hadir
                                            </span>
                                        @else
                                            <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded font-medium">
                                                Belum Absen
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        Belum ada karyawan di bagian ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <p class="text-gray-500">Belum ada bagian yang dipilih untuk jadwal ini</p>
            </div>
        @endforelse
    </div>

    <!-- Tombol Aksi -->
    <div class="flex flex-wrap gap-3 mt-8">
        @if($jadwal->status === 'draft')
            <form action="{{ route('penjadwalan.generate-presensi', $jadwal->id_jadwal) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Generate Presensi
                </button>
            </form>
        @elseif($jadwal->status === 'published')
            @php
                $batasAkhir = $jadwal->tanggal_pelaksanaan->addDays(5)->endOfDay();
                $bisaPerpanjang = now() <= $batasAkhir;
            @endphp
            @if($bisaPerpanjang)
                <form action="{{ route('penjadwalan.extend-presensi', $jadwal->id_jadwal) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Perpanjang 30 Menit
                    </button>
                </form>
            @endif
        @endif
        
        <a href="{{ route('penjadwalan.edit', $jadwal->id_jadwal) }}" class="flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Edit Jadwal
        </a>
        <button onclick="deleteConfirm({{ $jadwal->id_jadwal }})" class="flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Hapus Jadwal
        </button>
        <form id="delete-form-{{ $jadwal->id_jadwal }}" action="{{ route('penjadwalan.destroy', $jadwal->id_jadwal) }}" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
function deleteConfirm(id) {
    if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endsection
