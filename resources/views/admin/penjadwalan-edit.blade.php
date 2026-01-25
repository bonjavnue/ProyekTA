@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    
    <!-- <div class="mb-6">
        <a href="{{ route('penjadwalan.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1 inline-flex">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>
    </div> -->

    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-brand-white via-brand-white-700 to-brand-white px-8 py-6">
            <h1 class="text-2xl font-bold text-black">Edit Jadwal Pelatihan</h1>
            <p class="text-gray-500 text-sm mt-1">Ubah informasi jadwal pelatihan yang sudah ada</p>
        </div>

        <form action="{{ route('penjadwalan.update', $jadwal->id_jadwal) }}" method="POST" class="p-8">
            @csrf
            @method('PUT')

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0-12a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                        <div>
                            <h3 class="font-semibold text-red-800 mb-2">Terjadi Kesalahan</h3>
                            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Session Error (Duplikasi Jadwal) -->
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0-12a9 9 0 110 18 9 9 0 010-18z"></path></svg>
                        <div>
                            <h3 class="font-semibold text-red-800 mb-1">⚠️ Jadwal Sudah Terdaftar</h3>
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column - Form Fields -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Jenis Pelatihan -->
                    <div>
                        <label for="id_jenis" class="block text-sm font-semibold text-gray-700 mb-2">Jenis Pelatihan <span class="text-red-500">*</span></label>
                        <select id="id_jenis" name="id_jenis" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition bg-white" required>
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
                        <input type="date" id="tanggal_pelaksanaan" name="tanggal_pelaksanaan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ old('tanggal_pelaksanaan', $jadwal->tanggal_pelaksanaan->format('Y-m-d')) }}">
                        @error('tanggal_pelaksanaan')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Jam Mulai dan Selesai -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="jam_mulai" class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                            <input type="time" id="jam_mulai" name="jam_mulai" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ $jadwal->jam_mulai->format('H:i') }}">
                            @error('jam_mulai')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="jam_selesai" class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                            <input type="time" id="jam_selesai" name="jam_selesai" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ $jadwal->jam_selesai->format('H:i') }}">
                            @error('jam_selesai')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Tempat -->
                    <div>
                        <label for="tempat" class="block text-sm font-semibold text-gray-700 mb-2">Tempat Pelaksanaan <span class="text-red-500">*</span></label>
                        <input type="text" id="tempat" name="tempat" placeholder="Contoh: Aula Utama Lantai 2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ $jadwal->tempat }}">
                        @error('tempat')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tenggat Presensi -->
                    <div>
                        <label for="tenggat_presensi" class="block text-sm font-semibold text-gray-700 mb-2">Tenggat Presensi <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="tenggat_presensi" name="tenggat_presensi" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" required value="{{ $jadwal->tenggat_presensi->format('Y-m-d\TH:i') }}">
                        @error('tenggat_presensi')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select id="status" name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition bg-white" required>
                            <option value="draft" @selected($jadwal->status == 'draft')>Draft</option>
                            <option value="published" @selected($jadwal->status == 'published')>Published</option>
                            <option value="selesai" @selected($jadwal->status == 'selesai')>Selesai</option>
                        </select>
                        @error('status')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea id="catatan" name="catatan" rows="2" placeholder="Catatan tambahan untuk jadwal pelatihan ini" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition resize-none">{{ $jadwal->catatan }}</textarea>
                    </div>
                </div>

                <!-- Right Column - Info & Bagian Selection -->
                <div class="lg:col-span-1 space-y-6">
                    
                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 sticky top-6">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            <div>
                                <h3 class="font-semibold text-blue-900 mb-1 text-sm">Info Sistem</h3>
                                <p class="text-xs text-blue-800 leading-relaxed">
                                    Lokasi presensi sudah otomatis di-set ke <strong>Kantor Pusat</strong> dengan radius <strong>200 meter</strong>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Peserta -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Pilih Bagian <span class="text-red-500">*</span></label>
                        <div class="space-y-2 border border-gray-200 rounded-xl p-4 max-h-64 overflow-y-auto">
                            @forelse($bagians as $bagian)
                                <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded-lg transition">
                                    <input type="checkbox" name="bagians[]" value="{{ $bagian->id_bagian }}" class="w-4 h-4 text-brand-blue rounded border-gray-300 focus:ring-2 focus:ring-brand-blue cursor-pointer" @checked(in_array($bagian->id_bagian, $selectedBagians))>
                                    <span class="ml-3 text-gray-700 text-sm font-medium">{{ $bagian->nama_bagian }}</span>
                                </label>
                            @empty
                                <p class="text-gray-500 text-sm py-4 text-center">Belum ada bagian</p>
                            @endforelse
                        </div>
                        @error('bagians')
                            <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-8 border-t border-gray-200 mt-8">
                <a href="{{ route('penjadwalan.index') }}" class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition text-center">
                    Batal
                </a>
                <button type="submit" class="flex-1 px-6 py-3 bg-brand-blue text-white font-semibold rounded-lg hover:bg-blue-900 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
