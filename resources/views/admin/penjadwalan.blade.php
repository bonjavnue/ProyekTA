@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Penjadwalan Pelatihan</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola jadwal pelatihan dan assign karyawan.</p>
        </div>
        <a href="{{ route('penjadwalan.create') }}" class="flex items-center gap-2 bg-brand-blue hover:bg-blue-900 text-white font-medium py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Buat Jadwal Baru</span>
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

    <!-- Tabel Jadwal Pelatihan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">No.</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Jenis Pelatihan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Tempat</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Bagian</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($jadwalPelatihans as $key => $jadwal)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 text-sm text-gray-900">{{ $key + 1 }}</td>
                            <td class="px-6 py-3 text-sm">
                                <span class="font-medium text-gray-900">{{ $jadwal->JenisPelatihan->nama_jenis ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700">
                                {{ $jadwal->tanggal_pelaksanaan->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700">
                                {{ $jadwal->jam_mulai->format('H:i') ?? '-' }} - {{ $jadwal->jam_selesai->format('H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $jadwal->tempat ?? '-' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-700">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($jadwal->JadwalBagian as $jadwalBagian)
                                        <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">
                                            {{ $jadwalBagian->Bagian->nama_bagian ?? '-' }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-3 text-sm">
                                @php
                                    $statusColor = match($jadwal->status) {
                                        'draft' => 'bg-yellow-100 text-yellow-700',
                                        'published' => 'bg-blue-100 text-blue-700',
                                        'selesai' => 'bg-green-100 text-green-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="inline-block px-2 py-1 text-xs rounded font-medium {{ $statusColor }}">
                                    {{ ucfirst($jadwal->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('penjadwalan.show', $jadwal->id_jadwal) }}" class="text-blue-600 hover:text-blue-800 font-medium" title="Lihat Detail">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('penjadwalan.edit', $jadwal->id_jadwal) }}" class="text-amber-600 hover:text-amber-800 font-medium" title="Edit">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button onclick="deleteConfirm({{ $jadwal->id_jadwal }})" class="text-red-600 hover:text-red-800 font-medium" title="Hapus">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    <form id="delete-form-{{ $jadwal->id_jadwal }}" action="{{ route('penjadwalan.destroy', $jadwal->id_jadwal) }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                Belum ada jadwal pelatihan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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