@extends('layouts.admin')

@section('content')
<div class="container mx-auto max-w-2xl">
    
    <div class="mb-6">
        <a href="{{ route('penjadwalan.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-brand-blue to-blue-800 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">Edit Jadwal Pelatihan</h1>
        </div>

        <form action="{{ route('penjadwalan.update', $jadwal->id_jadwal) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Jenis Pelatihan -->
            <div>
                <label for="id_jenis" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pelatihan <span class="text-red-500">*</span></label>
                <select id="id_jenis" name="id_jenis" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required>
                    <option value="">-- Pilih Jenis Pelatihan --</option>
                    @foreach($jenisPelatihans as $jenis)
                        <option value="{{ $jenis->id_jenis }}" @selected($jenis->id_jenis == $jadwal->id_jenis)>{{ $jenis->nama_jenis }}</option>
                    @endforeach
                </select>
                @error('id_jenis')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal Pelaksanaan -->
            <div>
                <label for="tanggal_pelaksanaan" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pelaksanaan <span class="text-red-500">*</span></label>
                <input type="date" id="tanggal_pelaksanaan" name="tanggal_pelaksanaan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ $jadwal->tanggal_pelaksanaan->format('Y-m-d') }}">
                @error('tanggal_pelaksanaan')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Jam Mulai dan Selesai -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="jam_mulai" class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" id="jam_mulai" name="jam_mulai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ $jadwal->jam_mulai->format('H:i') }}">
                    @error('jam_mulai')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="jam_selesai" class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" id="jam_selesai" name="jam_selesai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ $jadwal->jam_selesai->format('H:i') }}">
                    @error('jam_selesai')
                        <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Tempat -->
            <div>
                <label for="tempat" class="block text-sm font-semibold text-gray-700 mb-2">Tempat Pelaksanaan <span class="text-red-500">*</span></label>
                <input type="text" id="tempat" name="tempat" placeholder="Contoh: Aula Utama Lantai 2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ $jadwal->tempat }}">
                @error('tempat')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tenggat Presensi -->
            <div>
                <label for="tenggat_presensi" class="block text-sm font-semibold text-gray-700 mb-2">Tenggat Presensi <span class="text-red-500">*</span></label>
                <input type="datetime-local" id="tenggat_presensi" name="tenggat_presensi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ $jadwal->tenggat_presensi->format('Y-m-d\TH:i') }}">
                @error('tenggat_presensi')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required>
                    <option value="draft" @selected($jadwal->status == 'draft')>Draft</option>
                    <option value="published" @selected($jadwal->status == 'published')>Published</option>
                    <option value="selesai" @selected($jadwal->status == 'selesai')>Selesai</option>
                </select>
                @error('status')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Bagian Peserta -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Bagian yang Akan Dilatih <span class="text-red-500">*</span></label>
                <div class="space-y-2 border border-gray-200 rounded-lg p-4">
                    @forelse($bagians as $bagian)
                        <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition">
                            <input type="checkbox" name="bagians[]" value="{{ $bagian->id_bagian }}" class="w-4 h-4 text-brand-blue rounded border-gray-300 focus:ring-2 focus:ring-brand-blue" @checked(in_array($bagian->id_bagian, $selectedBagians))>
                            <span class="ml-3 text-gray-700">{{ $bagian->nama_bagian }}</span>
                        </label>
                    @empty
                        <p class="text-gray-500 text-sm">Belum ada bagian</p>
                    @endforelse
                </div>
                @error('bagians')
                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Catatan -->
            <div>
                <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">Catatan</label>
                <textarea id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan (opsional)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition resize-none">{{ $jadwal->catatan }}</textarea>
            </div>

            <!-- Tombol -->
            <div class="flex gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('penjadwalan.index') }}" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-center">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-4 py-2 bg-brand-blue text-white font-medium rounded-lg hover:bg-blue-900 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
