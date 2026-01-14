@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Karyawan</h1>
            <p class="text-gray-500 text-sm mt-1">Manajemen informasi seluruh karyawan perusahaan.</p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:flex-none">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Cari nama atau ID..."
                    value="{{ request('search', '') }}"
                    class="w-full md:w-64 bg-white text-gray-700 border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue shadow-sm transition-all"
                >
            </div>
            
            <button onclick="openModal()" class="flex items-center gap-2 bg-brand-blue hover:bg-blue-900 text-white font-medium py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Karyawan</span>
            </button>
            
            <button onclick="openImportModal()" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Import Excel</span>
            </button>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="hidden mb-6 p-4 bg-white border border-red-200 rounded-xl shadow-sm flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-sm font-medium text-gray-700">
                <span id="selectedCount">0</span> item dipilih
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button 
                onclick="clearAllChecks()"
                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium text-sm"
            >
                Batal
            </button>
            <button 
                onclick="deleteSelectedItems()"
                class="flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm rounded-lg transition font-medium"
                title="Hapus Item Terpilih"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Hapus
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-bold tracking-wider">
                        <th class="p-4 w-4">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue cursor-pointer" title="Select all data">
                        </th>
                        <th class="p-4 cursor-pointer hover:text-brand-blue group transition-colors" onclick="sortColumn('id_karyawan')">
                            ID Karyawan
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-id_karyawan">↓</span>
                        </th>
                        <th class="p-4 cursor-pointer hover:text-brand-blue group transition-colors" onclick="sortColumn('nik')">
                            NIK
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-nik">↓</span>
                        </th>
                        <th class="p-4 cursor-pointer hover:text-brand-blue group transition-colors" onclick="sortColumn('nama_karyawan')">
                            Nama
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-nama_karyawan">↓</span>
                        </th>
                        <th class="p-4 cursor-pointer hover:text-brand-blue group transition-colors" onclick="sortColumn('id_bagian')">
                            Bagian/Divisi
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-id_bagian">↓</span>
                        </th>
                        <th class="p-4 cursor-pointer hover:text-brand-blue group transition-colors" onclick="sortColumn('status_karyawan')">
                            Status
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-status_karyawan">↓</span>
                        </th>
                        <th class="p-4">No. Telp</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($karyawans as $karyawan)
                    <tr class="hover:bg-blue-50/50 transition-colors duration-200 group" 
                        data-id="{{ $karyawan->id_karyawan }}" 
                        data-nik="{{ $karyawan->nik }}"
                        data-nama="{{ $karyawan->nama_karyawan }}"
                        data-bagian="{{ $karyawan->id_bagian }}"
                        data-bagian-nama="{{ $karyawan->Bagian->nama_bagian ?? '-' }}"
                        data-status="{{ $karyawan->status_karyawan }}"
                        data-telp="{{ $karyawan->no_telepon ?? '' }}">
                        <td class="p-4">
                            <input type="checkbox" class="rowCheckbox w-4 h-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue cursor-pointer" value="{{ $karyawan->id_karyawan }}">
                        </td>
                        <td class="p-4 font-semibold text-brand-blue">{{ $karyawan->id_karyawan }}</td>
                        <td class="p-4 font-medium text-gray-700">{{ $karyawan->nik }}</td>
                        <td class="p-4 font-medium text-gray-900">{{ $karyawan->nama_karyawan }}</td>
                        <td class="p-4">{{ $karyawan->Bagian->nama_bagian ?? '-' }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 
                                @if($karyawan->status_karyawan === 'Tetap') bg-green-100 text-green-700
                                @elseif($karyawan->status_karyawan === 'Kontrak') bg-blue-100 text-blue-700
                                @elseif($karyawan->status_karyawan === 'Cuti') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700
                                @endif
                                text-xs font-bold rounded-full uppercase">
                                {{ $karyawan->status_karyawan }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-500">{{ $karyawan->no_telepon ?? '-' }}</td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button
                                    onclick="openDetailModal('{{ $karyawan->id_karyawan }}', '{{ $karyawan->nik }}', '{{ $karyawan->nama_karyawan }}', '{{ $karyawan->Bagian->nama_bagian ?? '-' }}', '{{ $karyawan->status_karyawan }}', '{{ $karyawan->no_telepon ?? '-' }}')"
                                    class="p-1.5 rounded-md hover:bg-blue-50 text-gray-400 hover:text-brand-blue transition-colors"
                                    title="Lihat">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <button 
                                    onclick="openEditModal('{{ $karyawan->id_karyawan }}', '{{ $karyawan->nik }}', '{{ $karyawan->nama_karyawan }}', '{{ $karyawan->id_bagian }}', '{{ $karyawan->status_karyawan }}', '{{ $karyawan->no_telepon }}')"
                                    class="p-1.5 rounded-md hover:bg-yellow-50 text-gray-400 hover:text-brand-yellow transition-colors"
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button 
                                    onclick="openDeleteModal('{{ $karyawan->id_karyawan }}', '{{ $karyawan->nama_karyawan }}')"
                                    class="p-1.5 rounded-md hover:bg-red-50 text-gray-400 hover:text-brand-red transition-colors"
                                    title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-500">
                            Tidak ada data karyawan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 border-t border-gray-200 px-4 py-4 sm:px-6">
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Pagination Info dan Links -->
                <div class="flex-1">
                    <div class="text-sm text-gray-600 mb-3">
                        Menampilkan <span class="font-bold text-gray-900">{{ $karyawans->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-gray-900">{{ $karyawans->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-900">{{ $karyawans->total() }}</span> hasil
                    </div>
                    {{ $karyawans->links('pagination.custom') }}
                </div>

                <!-- Per Page Dropdown -->
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Tampilkan per halaman:</label>
                    <select onchange="changePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm bg-white font-medium min-w-max">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 / page</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 / page</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 / page</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 / page</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Karyawan -->
<div id="tambahKaryawanModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full animate-fade-in max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-brand-blue to-blue-900 px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-bold text-white">Tambah Karyawan</h2>
        </div>
        
        <form id="formTambahKaryawan" class="p-6">
            @csrf
            
            <div class="mb-4">
                <label for="id_karyawan" class="block text-sm font-medium text-gray-700 mb-2">ID Karyawan *</label>
                <input 
                    type="number" 
                    id="id_karyawan" 
                    name="id_karyawan" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    placeholder="Masukkan ID Karyawan"
                    required
                >
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>
            
            <div class="mb-4">
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-2">NIK *</label>
                <input 
                    type="text" 
                    id="nik" 
                    name="nik" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    placeholder="Masukkan NIK"
                    required
                >
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-4">
                <label for="nama_karyawan" class="block text-sm font-medium text-gray-700 mb-2">Nama Karyawan *</label>
                <input 
                    type="text" 
                    id="nama_karyawan" 
                    name="nama_karyawan" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    placeholder="Masukkan nama karyawan"
                    required
                >
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-4">
                <label for="id_bagian" class="block text-sm font-medium text-gray-700 mb-2">Bagian/Divisi *</label>
                <select 
                    id="id_bagian" 
                    name="id_bagian" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    required
                >
                    <option value="">-- Pilih Bagian --</option>
                    @foreach($bagians ?? [] as $bagian)
                        <option value="{{ $bagian->id_bagian }}">{{ $bagian->nama_bagian }}</option>
                    @endforeach
                </select>
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Karyawan *</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status_karyawan" value="Tetap" required class="w-4 h-4 text-brand-blue focus:ring-brand-blue">
                        <span class="text-gray-700">Tetap</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status_karyawan" value="Kontrak" required class="w-4 h-4 text-brand-blue focus:ring-brand-blue">
                        <span class="text-gray-700">Kontrak</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status_karyawan" value="Cuti" required class="w-4 h-4 text-brand-blue focus:ring-brand-blue">
                        <span class="text-gray-700">Cuti</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="status_karyawan" value="Tidak Aktif" required class="w-4 h-4 text-brand-blue focus:ring-brand-blue">
                        <span class="text-gray-700">Tidak Aktif</span>
                    </label>
                </div>
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-6">
                <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                <input 
                    type="tel" 
                    id="no_telepon" 
                    name="no_telepon" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    placeholder="0812-3456-7890"
                >
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="flex gap-3 justify-end">
                <button 
                    type="button" 
                    onclick="closeTambahModal()"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-brand-blue hover:bg-blue-900 text-white rounded-lg transition font-medium flex items-center gap-2"
                    id="submitBtn"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div id="editKaryawanModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full animate-fade-in max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-gradient-to-r from-brand-blue to-blue-900 px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-bold text-white">Edit Karyawan</h2>
        </div>
        
        <form id="formEditKaryawan" class="p-6">
            @csrf
            
            <input type="hidden" id="edit_id_karyawan" name="edit_id_karyawan">
            
            <div class="mb-4">
                <label for="edit_nik" class="block text-sm font-medium text-gray-700 mb-2">NIK *</label>
                <input 
                    type="text" 
                    id="edit_nik" 
                    name="nik" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    placeholder="Masukkan NIK"
                    required
                >
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-4">
                <label for="edit_nama_karyawan" class="block text-sm font-medium text-gray-700 mb-2">Nama Karyawan *</label>
                <input 
                    type="text" 
                    id="edit_nama_karyawan" 
                    name="nama_karyawan" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    placeholder="Masukkan nama karyawan"
                    required
                >
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-4">
                <label for="edit_id_bagian" class="block text-sm font-medium text-gray-700 mb-2">Bagian/Divisi *</label>
                <select 
                    id="edit_id_bagian" 
                    name="id_bagian" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    required
                >
                    <option value="">-- Pilih Bagian --</option>
                    @foreach($bagians ?? [] as $bagian)
                        <option value="{{ $bagian->id_bagian }}">{{ $bagian->nama_bagian }}</option>
                    @endforeach
                </select>
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Karyawan *</label>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="edit_status_karyawan" value="Tetap" required class="w-4 h-4 text-brand-blue focus:ring-brand-blue">
                        <span class="text-gray-700">Tetap</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="edit_status_karyawan" value="Kontrak" required class="w-4 h-4 text-brand-blue focus:ring-brand-blue">
                        <span class="text-gray-700">Kontrak</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="edit_status_karyawan" value="Cuti" required class="w-4 h-4 text-brand-blue focus:ring-brand-blue">
                        <span class="text-gray-700">Cuti</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" name="edit_status_karyawan" value="Tidak Aktif" required class="w-4 h-4 text-brand-blue focus:ring-brand-blue">
                        <span class="text-gray-700">Tidak Aktif</span>
                    </label>
                </div>
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-6">
                <label for="edit_no_telepon" class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                <input 
                    type="tel" 
                    id="edit_no_telepon" 
                    name="no_telepon" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    placeholder="0812-3456-7890"
                >
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="flex gap-3 justify-end">
                <button 
                    type="button" 
                    onclick="closeEditModal()"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-brand-blue hover:bg-blue-900 text-white rounded-lg transition font-medium flex items-center gap-2"
                    id="editSubmitBtn"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Karyawan -->
<div id="detailKaryawanModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full animate-fade-in">
        <div class="bg-gradient-to-r from-brand-blue to-blue-900 px-6 py-4 rounded-t-lg flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">Detail Karyawan</h2>
        </div>
        
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">ID Karyawan</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-800 font-semibold" id="detail_id_karyawan">-</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">NIK</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-800 font-semibold" id="detail_nik">-</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Karyawan</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-800 font-semibold" id="detail_nama_karyawan">-</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Bagian/Divisi</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-800" id="detail_bagian">-</p>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-800" id="detail_status">-</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-800" id="detail_no_telepon">-</p>
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <button 
                    type="button" 
                    onclick="closeDetailModal()"
                    class="px-4 py-2 bg-brand-blue hover:bg-blue-900 text-white rounded-lg transition font-medium"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full animate-fade-in">
        <div class="bg-red-50 px-6 py-4 border-b border-red-200 rounded-t-lg">
            <h2 class="text-lg font-bold text-red-900">Hapus Karyawan</h2>
        </div>
        
        <div class="p-6">
            <p class="text-gray-700 mb-2">
                Apakah Anda yakin ingin menghapus karyawan berikut?
            </p>
            <p class="text-lg font-semibold text-gray-900 mb-6 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <span id="deleteItemName"></span>
            </p>
            <p class="text-sm text-gray-600 mb-6">
                <svg class="w-5 h-5 inline text-amber-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="flex gap-3 justify-end">
                <button 
                    type="button" 
                    onclick="closeDeleteModal()"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    onclick="confirmDelete()"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium flex items-center gap-2"
                    id="deleteConfirmBtn"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
</style>

<!-- Modal Import Excel -->
<div id="importExcelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full animate-fade-in">
        <div class="sticky top-0 bg-gradient-to-r from-green-600 to-green-900 px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-bold text-white">Import Karyawan dari Excel</h2>
        </div>
        
        <form id="formImportExcel" class="p-6">
            @csrf
            
            <div class="mb-4">
                <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">File Excel/CSV *</label>
                <input 
                    type="file" 
                    id="excel_file" 
                    name="excel_file" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition"
                    accept=".xlsx,.xls,.csv"
                    required
                >
                <p class="text-xs text-gray-500 mt-2">Format: CSV, XLS, atau XLSX (Max 5MB)</p>
                <p class="text-xs text-gray-600 mt-2"><strong>Format CSV:</strong> id_karyawan, nik, nama_karyawan, id_bagian, status_karyawan, no_telepon</p>
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-blue-800">
                    <strong>Catatan:</strong> Pastikan file CSV Anda memiliki header sesuai format di atas. 
                    <a href="#" class="text-blue-600 underline" onclick="downloadTemplate(event)">Download template CSV</a>
                </p>
            </div>

            <div class="flex gap-3 justify-end">
                <button 
                    type="button" 
                    onclick="closeImportModal()"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium flex items-center gap-2"
                    id="importSubmitBtn"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const tambahModal = document.getElementById('tambahKaryawanModal');
    const editModal = document.getElementById('editKaryawanModal');
    const detailModal = document.getElementById('detailKaryawanModal');
    const deleteModal = document.getElementById('deleteConfirmModal');
    const importModal = document.getElementById('importExcelModal');
    const formTambah = document.getElementById('formTambahKaryawan');
    const formEdit = document.getElementById('formEditKaryawan');
    const formImport = document.getElementById('formImportExcel');
    let deleteIdKaryawan = null;

    // === TAMBAH MODAL ===
    function openModal() {
        tambahModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeTambahModal() {
        tambahModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        formTambah.reset();
        clearErrors(formTambah);
    }

    // === IMPORT MODAL ===
    function openImportModal() {
        importModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeImportModal() {
        importModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        formImport.reset();
        clearErrors(formImport);
    }

    function downloadTemplate(e) {
        e.preventDefault();
        const csv = 'id_karyawan,nik,nama_karyawan,id_bagian,status_karyawan,no_telepon\n1,3171051203980001,John Doe,1,Tetap,081234567890\n2,3171051203980002,Jane Smith,2,Kontrak,081234567891';
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'template_karyawan.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // === EDIT MODAL ===
    function openEditModal(id, nik, nama, bagian, status, telp) {
        document.getElementById('edit_id_karyawan').value = id;
        document.getElementById('edit_nik').value = nik;
        document.getElementById('edit_nama_karyawan').value = nama;
        document.getElementById('edit_id_bagian').value = bagian;
        document.querySelector(`input[name="edit_status_karyawan"][value="${status}"]`).checked = true;
        document.getElementById('edit_no_telepon').value = telp || '';
        
        editModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        editModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        formEdit.reset();
        clearErrors(formEdit);
    }

    // === DETAIL MODAL ===
    function openDetailModal(id, nik, nama, bagian, status, telp) {
        document.getElementById('detail_id_karyawan').textContent = id;
        document.getElementById('detail_nik').textContent = nik;
        document.getElementById('detail_nama_karyawan').textContent = nama;
        document.getElementById('detail_bagian').textContent = bagian;
        document.getElementById('detail_status').textContent = status;
        document.getElementById('detail_no_telepon').textContent = telp || '-';
        
        detailModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        detailModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // === DELETE MODAL ===
    function openDeleteModal(id, nama) {
        deleteIdKaryawan = id;
        document.getElementById('deleteItemName').textContent = nama;
        deleteModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        deleteModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        deleteIdKaryawan = null;
    }

    async function confirmDelete() {
        const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
        deleteConfirmBtn.disabled = true;
        deleteConfirmBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menghapus...</span>';

        try {
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            
            const response = await fetch(`/admin/datakaryawan/${deleteIdKaryawan}/delete`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                alert('Karyawan berhasil dihapus');
                closeDeleteModal();
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus data');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data: ' + error.message);
        } finally {
            deleteConfirmBtn.disabled = false;
            deleteConfirmBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Ya, Hapus</span>';
        }
    }

    // Close modal dengan ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!tambahModal.classList.contains('hidden')) closeTambahModal();
            if (!editModal.classList.contains('hidden')) closeEditModal();
            if (!detailModal.classList.contains('hidden')) closeDetailModal();
            if (!deleteModal.classList.contains('hidden')) closeDeleteModal();
        }
    });

    // === FORM SUBMISSION ===
    function clearErrors(form) {
        form.querySelectorAll('.error-message').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }

    // Tambah Karyawan
    formTambah.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors(formTambah);

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menyimpan...</span>';

        const formData = new FormData(formTambah);

        try {
            const response = await fetch('/admin/datakaryawan/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok) {
                alert('Karyawan berhasil ditambahkan');
                closeTambahModal();
                location.reload();
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorElement = document.querySelector(`#${field}`).parentElement.querySelector('.error-message');
                        if (errorElement) {
                            errorElement.textContent = data.errors[field][0];
                            errorElement.classList.remove('hidden');
                        }
                    });
                } else {
                    alert(data.message || 'Gagal menyimpan data');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>Simpan</span>';
        }
    });

    // Edit Karyawan
    formEdit.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors(formEdit);

        const idKaryawan = document.getElementById('edit_id_karyawan').value;
        const editSubmitBtn = document.getElementById('editSubmitBtn');
        editSubmitBtn.disabled = true;
        editSubmitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menyimpan...</span>';

        const csrfToken = document.querySelector('input[name="_token"]').value;
        const data = {
            'nik': document.getElementById('edit_nik').value,
            'nama_karyawan': document.getElementById('edit_nama_karyawan').value,
            'id_bagian': document.getElementById('edit_id_bagian').value,
            'status_karyawan': document.querySelector('input[name="edit_status_karyawan"]:checked').value,
            'no_telepon': document.getElementById('edit_no_telepon').value,
        };

        console.log('Edit data:', data);

        try {
            const response = await fetch(`/admin/datakaryawan/${idKaryawan}/update`, {
                method: 'PUT',
                body: JSON.stringify(data),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const respData = await response.json();

            if (response.ok && respData.success) {
                alert('Karyawan berhasil diperbarui');
                closeEditModal();
                location.reload();
            } else {
                if (respData.errors) {
                    Object.keys(respData.errors).forEach(field => {
                        const selector = `#edit_${field}`;
                        const element = document.querySelector(selector);
                        if (element) {
                            const errorElement = element.parentElement.querySelector('.error-message');
                            if (errorElement) {
                                errorElement.textContent = respData.errors[field][0];
                                errorElement.classList.remove('hidden');
                            }
                        }
                    });
                } else {
                    alert(respData.message || 'Gagal menyimpan perubahan');
                }
                console.error('Response data:', respData);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan perubahan: ' + error.message);
        } finally {
            editSubmitBtn.disabled = false;
            editSubmitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>Simpan Perubahan</span>';
        }
    });

    // === PAGINATION & SEARCH ===
    function changePerPage(value) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    }

    function loadPageViaAjax(event, href) {
        event.preventDefault();
        
        fetch(href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newTableBody = doc.querySelector('tbody');
            const newPagination = doc.querySelector('nav[role="navigation"]');
            
            const currentTableBody = document.querySelector('tbody');
            const currentPagination = document.querySelector('nav[role="navigation"]');
            
            if (newTableBody) {
                currentTableBody.innerHTML = newTableBody.innerHTML;
            }
            
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }
            
            window.history.replaceState({}, '', href);
            document.querySelector('table').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => console.error('Error:', error));
    }

    // Search dengan AJAX
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(() => {
                const searchQuery = e.target.value.trim();
                const perPage = new URL(window.location).searchParams.get('per_page') || '10';
                
                const url = new URL('/admin/datakaryawan', window.location.origin);
                
                if (searchQuery) {
                    url.searchParams.set('search', searchQuery);
                } else {
                    url.searchParams.delete('search');
                }
                
                url.searchParams.set('per_page', perPage);
                
                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const newTableBody = doc.querySelector('tbody');
                    const newPagination = doc.querySelector('nav[role="navigation"]');
                    
                    const currentTableBody = document.querySelector('tbody');
                    const currentPagination = document.querySelector('nav[role="navigation"]');
                    
                    if (newTableBody) {
                        currentTableBody.innerHTML = newTableBody.innerHTML;
                    }
                    
                    if (newPagination && currentPagination) {
                        currentPagination.innerHTML = newPagination.innerHTML;
                    }
                    
                    window.history.replaceState({}, '', url.toString());
                })
                .catch(error => console.error('Error:', error));
            }, 300);
        });
    }

    // === SORTING ===
    let currentSort = new URL(window.location).searchParams.get('sort_by') || 'created_at';
    let currentOrder = new URL(window.location).searchParams.get('sort_order') || 'desc';

    function updateSortIndicators() {
        document.querySelectorAll('[id^="sort-"]').forEach(el => {
            el.textContent = '↓';
            el.classList.remove('text-brand-blue');
            el.classList.add('text-gray-300');
        });
        
        const currentIndicator = document.getElementById(`sort-${currentSort}`);
        if (currentIndicator) {
            currentIndicator.textContent = currentOrder === 'asc' ? '↑' : '↓';
            currentIndicator.classList.remove('text-gray-300');
            currentIndicator.classList.add('text-brand-blue');
        }
    }

    function sortColumn(column) {
        if (currentSort === column) {
            currentOrder = currentOrder === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort = column;
            currentOrder = 'asc';
        }
        
        loadSortedData();
    }

    function loadSortedData() {
        const url = new URL(window.location);
        const searchQuery = document.getElementById('searchInput').value.trim();
        const perPage = url.searchParams.get('per_page') || '10';
        
        url.searchParams.set('sort_by', currentSort);
        url.searchParams.set('sort_order', currentOrder);
        url.searchParams.set('per_page', perPage);
        
        if (searchQuery) {
            url.searchParams.set('search', searchQuery);
        } else {
            url.searchParams.delete('search');
        }
        
        url.searchParams.delete('page');
        
        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newTableBody = doc.querySelector('tbody');
            const newPagination = doc.querySelector('nav[role="navigation"]');
            
            const currentTableBody = document.querySelector('tbody');
            const currentPagination = document.querySelector('nav[role="navigation"]');
            
            if (newTableBody) {
                currentTableBody.innerHTML = newTableBody.innerHTML;
            }
            
            if (newPagination && currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }
            
            window.history.replaceState({}, '', url.toString());
            updateSortIndicators();
            document.querySelector('table').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => console.error('Error:', error));
    }

    updateSortIndicators();

    // === BULK ACTIONS ===
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    let totalDataCount = {{ $karyawans->total() }};
    let allDataSelected = false;
    let allDataItems = [];

    function updateBulkActionsBar() {
        const checkedCount = allDataSelected ? totalDataCount : document.querySelectorAll('.rowCheckbox:checked').length;
        const bulkBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');
        
        if (checkedCount > 0) {
            selectedCount.textContent = `${checkedCount} dari ${totalDataCount}`;
            bulkBar.classList.remove('hidden');
        } else {
            bulkBar.classList.add('hidden');
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', async function() {
            if (this.checked) {
                try {
                    const perPage = new URL(window.location).searchParams.get('per_page') || '10';
                    const searchQuery = document.getElementById('searchInput').value.trim();
                    
                    const url = new URL('/admin/datakaryawan', window.location.origin);
                    url.searchParams.set('per_page', 999999);
                    
                    if (searchQuery) {
                        url.searchParams.set('search', searchQuery);
                    }
                    
                    const response = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    allDataItems = [];
                    doc.querySelectorAll('tbody tr').forEach(row => {
                        const checkbox = row.querySelector('.rowCheckbox');
                        if (checkbox) {
                            allDataItems.push({
                                id: checkbox.value,
                                nama: row.dataset.nama,
                                nik: row.dataset.nik
                            });
                        }
                    });
                    
                    document.querySelectorAll('.rowCheckbox').forEach(checkbox => {
                        checkbox.checked = true;
                    });
                    
                    allDataSelected = true;
                    updateBulkActionsBar();
                } catch (error) {
                    console.error('Error fetching all data:', error);
                    alert('Gagal mengambil semua data');
                    this.checked = false;
                }
            } else {
                document.querySelectorAll('.rowCheckbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                allDataSelected = false;
                allDataItems = [];
                updateBulkActionsBar();
            }
        });
    }

    document.querySelectorAll('.rowCheckbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!allDataSelected) {
                updateBulkActionsBar();
            }
        });
    });

    function clearAllChecks() {
        selectAllCheckbox.checked = false;
        document.querySelectorAll('.rowCheckbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        allDataSelected = false;
        allDataItems = [];
        updateBulkActionsBar();
    }

    function getSelectedItems() {
        if (allDataSelected) {
            return allDataItems;
        }
        
        const selected = [];
        document.querySelectorAll('.rowCheckbox:checked').forEach(checkbox => {
            const row = checkbox.closest('tr');
            selected.push({
                id: checkbox.value,
                nama: row.dataset.nama,
                nik: row.dataset.nik
            });
        });
        return selected;
    }

    function deleteSelectedItems() {
        const selected = getSelectedItems();
        if (selected.length === 0) {
            alert('Tidak ada item yang dipilih');
            return;
        }
        
        const itemsPreview = selected.length > 5 
            ? selected.slice(0, 5).map(item => item.nama).join(', ') + ` ... (+${selected.length - 5} lainnya)`
            : selected.map(item => item.nama).join(', ');
        
        if (confirm(`Yakin hapus ${selected.length} karyawan?\n\n${itemsPreview}`)) {
            bulkDeleteItems(selected.map(item => item.id));
        }
    }

    async function bulkDeleteItems(ids) {
        try {
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            
            for (const id of ids) {
                await fetch(`/admin/datakaryawan/${id}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
            }
            
            alert(`${ids.length} karyawan berhasil dihapus`);
            location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data: ' + error.message);
        }
    }

    // === IMPORT EXCEL FORM ===
    formImport.addEventListener('submit', async function(e) {
        e.preventDefault();

        const importSubmitBtn = document.getElementById('importSubmitBtn');
        importSubmitBtn.disabled = true;
        importSubmitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Mengimpor...</span>';

        const formData = new FormData(formImport);

        try {
            const response = await fetch('/admin/datakaryawan/import-excel', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok && data.success) {
                let message = data.message;
                if (data.error_count > 0) {
                    message += `\n\nPeringatan: ${data.error_count} baris gagal diimpor:\n${data.errors.slice(0, 5).join('\n')}`;
                    if (data.error_count > 5) {
                        message += `\n... dan ${data.error_count - 5} error lainnya`;
                    }
                }
                alert(message);
                closeImportModal();
                location.reload();
            } else {
                alert(data.message || 'Gagal mengimpor file');
                console.error('Response:', data);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengimpor file: ' + error.message);
        } finally {
            importSubmitBtn.disabled = false;
            importSubmitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> <span>Import</span>';
        }
    });
</script>
@endsection