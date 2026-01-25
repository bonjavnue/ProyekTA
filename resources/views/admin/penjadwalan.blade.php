@extends('layouts.admin')

@section('content')
<div x-data="penjadwalanSearch()" class="container mx-auto">
    
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

    <!-- Filters -->
    <div class="flex flex-col lg:flex-row gap-6 mb-10 items-end justify-between">
        <!-- Search Input -->
        <div class="flex flex-col gap-1.5 w-full lg:w-72">
            <label class="text-sm font-semibold text-gray-700">Cari Pelatihan</label>
            <input type="text"
                   x-model="searchQuery"
                   placeholder="Ketik nama pelatihan..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none text-sm" />
        </div>

        <!-- Date Range -->
        <div class="flex flex-col gap-4 flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-600 block mb-1">Dari Tanggal</label>
                    <input type="date" 
                           x-model="dateFrom"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none text-sm" />
                </div>
                <div>
                    <label class="text-xs text-gray-600 block mb-1">Sampai Tanggal</label>
                    <input type="date" 
                           x-model="dateTo"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none text-sm" />
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Jadwal Pelatihan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 cursor-pointer hover:text-brand-blue group transition-colors" @click="sortColumn('no')">
                            No.
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-no">↓</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Jenis Pelatihan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 cursor-pointer hover:text-brand-blue group transition-colors" @click="sortColumn('tanggal_pelaksanaan')">
                            Tanggal
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-tanggal_pelaksanaan">↓</span>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Tempat</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Bagian</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 cursor-pointer hover:text-brand-blue group transition-colors" @click="sortColumn('status')">
                            Status
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-status">↓</span>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="(jadwal, index) in filteredJadwals" :key="jadwal.id_jadwal">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 text-sm text-gray-900" x-text="index + 1"></td>
                            <td class="px-6 py-3 text-sm">
                                <span class="font-medium text-gray-900" x-text="jadwal.nama_jenis"></span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700">
                                <span x-text="formatDate(jadwal.tanggal_pelaksanaan)"></span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700">
                                <span x-text="jadwal.jam_mulai + ' - ' + jadwal.jam_selesai"></span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700" x-text="jadwal.tempat"></td>
                            <td class="px-6 py-3 text-sm text-gray-700">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($jadwalPelatihans as $jadwal)
                                        @foreach($jadwal->JadwalBagian as $jadwalBagian)
                                            <template x-if="jadwal.id_jadwal === {{ $jadwal->id_jadwal }}">
                                                <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">
                                                    {{ $jadwalBagian->Bagian->nama_bagian ?? '-' }}
                                                </span>
                                            </template>
                                        @endforeach
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-3 text-sm">
                                <span class="inline-block px-2 py-1 text-xs rounded font-medium" :class="getStatusColor(jadwal.status)" x-text="jadwal.status.charAt(0).toUpperCase() + jadwal.status.slice(1)"></span>
                            </td>
                            <td class="px-6 py-3 text-center text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <a :href="`/admin/penjadwalan/${jadwal.id_jadwal}`" class="text-blue-600 hover:text-blue-800 font-medium" title="Lihat Detail">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a :href="`/admin/penjadwalan/${jadwal.id_jadwal}/edit`" class="text-amber-600 hover:text-amber-800 font-medium" title="Edit">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button @click="deleteConfirm(jadwal.id_jadwal)" class="text-red-600 hover:text-red-800 font-medium" title="Hapus">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    <form :id="'delete-form-' + jadwal.id_jadwal" :action="`/admin/penjadwalan/${jadwal.id_jadwal}`" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredJadwals.length === 0">
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-gray-500" x-show="searchQuery.length > 0 || dateFrom || dateTo">Tidak ada jadwal yang cocok dengan pencarian</p>
                            <p class="text-gray-500" x-show="searchQuery.length === 0 && !dateFrom && !dateTo">Belum ada jadwal pelatihan</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="bg-white-50 border-t border-white-200 px-4 py-4 sm:px-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Pagination Info -->
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-bold text-gray-900">{{ $jadwalPelatihans->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-gray-900">{{ $jadwalPelatihans->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-900">{{ $jadwalPelatihans->total() }}</span> hasil
                </div>

                <!-- Per Page Dropdown -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 whitespace-nowrap">Tampilkan per halaman</label>
                    <select onchange="changePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm bg-white font-medium">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4">
                {{ $jadwalPelatihans->links('pagination.custom') }}
            </div>
        </div>
            </div>
        </div>
    </div>
</div>

<script>
function changePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

function penjadwalanSearch() {
    return {
        searchQuery: '',
        dateFrom: '',
        dateTo: '',
        sortBy: null,
        sortOrder: null,
        jadwals: @json($jadwalForJs),
        
        sortColumn(column) {
            if (this.sortBy === column) {
                // Toggle arah: desc ↔ asc
                this.sortOrder = this.sortOrder === 'desc' ? 'asc' : 'desc';
            } else {
                // Klik kolom baru: mulai dari desc
                this.sortBy = column;
                this.sortOrder = 'desc';
            }
            this.updateSortIndicators();
        },
        
        updateSortIndicators() {
            this.$nextTick(() => {
                document.querySelectorAll('[id^="sort-"]').forEach(el => {
                    el.textContent = '↓';
                    el.classList.remove('text-brand-blue');
                    el.classList.add('text-gray-300');
                });
                
                const currentIndicator = document.getElementById(`sort-${this.sortBy}`);
                if (currentIndicator && this.sortBy) {
                    currentIndicator.textContent = this.sortOrder === 'asc' ? '↑' : '↓';
                    currentIndicator.classList.remove('text-gray-300');
                    currentIndicator.classList.add('text-brand-blue');
                }
            });
        },
        
        get filteredJadwals() {
            let filtered = this.jadwals;
            
            // Filter by search query
            if (this.searchQuery.trim() !== '') {
                const search = this.searchQuery.toLowerCase().trim();
                filtered = filtered.filter(jadwal => 
                    jadwal.nama_jenis.toLowerCase().includes(search) ||
                    jadwal.tempat.toLowerCase().includes(search)
                );
            }
            
            // Filter by date range
            if (this.dateFrom || this.dateTo) {
                filtered = filtered.filter(jadwal => {
                    const jadwalDate = new Date(jadwal.tanggal_pelaksanaan);
                    
                    if (this.dateFrom && jadwalDate < new Date(this.dateFrom)) {
                        return false;
                    }
                    
                    if (this.dateTo) {
                        const dateTo = new Date(this.dateTo);
                        dateTo.setHours(23, 59, 59, 999);
                        if (jadwalDate > dateTo) {
                            return false;
                        }
                    }
                    
                    return true;
                });
            }
            
            // Sorting (hanya jika ada yang di-sort)
            if (this.sortBy) {
                filtered.sort((a, b) => {
                    let aValue, bValue;
                    
                    if (this.sortBy === 'no') {
                        // Sort berdasarkan urutan di array (sebagai no)
                        aValue = this.jadwals.indexOf(a);
                        bValue = this.jadwals.indexOf(b);
                    } else if (this.sortBy === 'tanggal_pelaksanaan') {
                        aValue = new Date(a.tanggal_pelaksanaan);
                        bValue = new Date(b.tanggal_pelaksanaan);
                    } else if (this.sortBy === 'status') {
                        aValue = a.status.toLowerCase();
                        bValue = b.status.toLowerCase();
                    }
                    
                    if (this.sortOrder === 'asc') {
                        if (aValue > bValue) return 1;
                        if (aValue < bValue) return -1;
                        return 0;
                    } else {
                        if (aValue < bValue) return 1;
                        if (aValue > bValue) return -1;
                        return 0;
                    }
                });
            }
            
            return filtered;
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
        },
        
        getStatusColor(status) {
            if (status === 'draft') {
                return 'bg-yellow-100 text-yellow-700';
            } else if (status === 'published') {
                return 'bg-blue-100 text-blue-700';
            } else if (status === 'selesai') {
                return 'bg-green-100 text-green-700';
            }
            return 'bg-gray-100 text-gray-700';
        },
        
        deleteConfirm(id) {
            if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    };
}
</script>
@endsection