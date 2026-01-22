@extends('layouts.admin')

@section('content')
<!-- Material Symbols -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
</style>

<div x-data="kehadiranLiveSearch()" class="container mx-auto">    

    <!-- <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Manajemen Kehadiran</h2>
        <p class="text-gray-500 mt-1">Pilih jadwal pelatihan untuk mengelola kehadiran karyawan.</p>
    </div> -->
    <div class="mb-6"> <h2 class="text-2xl font-bold text-gray-800">Manajemen Kehadiran</h2> <p class="text-gray-500 text-sm mt-1">Pilih jadwal pelatihan untuk mengelola kehadiran karyawan.</p>
</div>

    <!-- Filters & Export -->
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
            <!-- <label class="text-sm font-semibold text-gray-700">Rentang Tanggal</label> -->
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

        <!-- Export Button -->
        <!-- <button @click="openExportModal()"
                class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/20 transition-all w-full lg:w-auto whitespace-nowrap">
            <span class="material-symbols-outlined text-[20px]">file_download</span>
            Export Laporan
        </button> -->
        <button @click="openExportModal()"
                class="flex items-center justify-center gap-2 bg-brand-blue hover:bg-blue-900 text-white px-4 py-2 rounded-lg font-medium shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 w-full lg:w-auto whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            <span>Export Laporan</span>
        </button>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="jadwal in filteredJadwals" :key="jadwal.id_jadwal">
            <a :href="`{{ url('admin/kehadiran') }}/${jadwal.id_jadwal}`"
               class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col border border-gray-100">
                <!-- Card Content -->
                <div class="p-6 flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold uppercase tracking-wider">
                            <span x-text="jadwal.tanggal_pelaksanaan"></span>
                        </span>
                        <span class="text-gray-400 text-xs font-medium">
                            <span x-text="jadwal.jam_mulai + ' - ' + jadwal.jam_selesai"></span>
                        </span>
                    </div>

                    <!-- Title with Status Badge -->
                    <div class="flex items-center gap-2 mb-2">
                        <h3 class="text-xl font-bold text-gray-800" x-text="jadwal.nama_jenis"></h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase"
                              :class="getStatusBadgeClass(jadwal.status)"
                              x-text="jadwal.status">
                        </span>
                    </div>

                    <!-- Location -->
                    <p class="text-gray-500 text-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">location_on</span>
                        Tempat: <span x-text="jadwal.tempat"></span>
                    </p>
                </div>

                <!-- Progress Bar Section -->
                <div class="px-6 pb-6 pt-2 border-t border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[11px] font-bold text-green-600 uppercase tracking-tighter">
                            <span x-text="jadwal.hadir_count"></span> HADIR
                        </span>
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-tighter">
                            <span x-text="jadwal.belum_absen_count"></span> BELUM
                        </span>
                    </div>
                    <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full transition-all"
                             :style="`width: ${jadwal.persentase_hadir}%`">
                        </div>
                    </div>
                </div>
            </a>
        </template>

        <!-- Empty State -->
        <div x-show="filteredJadwals.length === 0" class="col-span-full text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg font-medium" x-show="searchQuery.length > 0">Tidak ada jadwal yang cocok dengan pencarian</p>
            <p class="text-gray-500 text-lg font-medium" x-show="searchQuery.length === 0 && (dateFrom || dateTo)">Tidak ada jadwal dalam rentang tanggal ini</p>
            <p class="text-gray-500 text-lg font-medium" x-show="searchQuery.length === 0 && !dateFrom && !dateTo">Belum ada jadwal pelatihan</p>
            <p class="text-gray-400 text-sm mt-2" x-show="searchQuery.length > 0 || dateFrom || dateTo">Coba ubah kriteria pencarian atau filter Anda</p>
        </div>
    </div>

    <!-- Export Modal -->
    <div x-show="exportModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800">Export Laporan Kehadiran</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format</label>
                    <select x-model="exportFormat" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal (Opsional)</label>
                    <input type="date" 
                           x-model="exportDateFrom"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal (Opsional)</label>
                    <input type="date" 
                           x-model="exportDateTo"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex gap-3 justify-end">
                <button @click="exportModalOpen = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium text-sm transition-all">
                    Batal
                </button>
                <button @click="exportReport()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-all">
                    Export
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function kehadiranLiveSearch() {
    return {
        searchQuery: '',
        dateFrom: '',
        dateTo: '',
        exportModalOpen: false,
        exportFormat: 'pdf',
        exportDateFrom: '',
        exportDateTo: '',
        jadwals: @json($jadwalsForJs),
        
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
                    const jadwalDate = this.parseDate(jadwal.tanggal_pelaksanaan);
                    
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
            
            return filtered;
        },
        
        
        parseDate(dateString) {
            const date = new Date(dateString);
            return date;
        },
        
        resetDateFilter() {
            this.dateFrom = '';
            this.dateTo = '';
        },
        
        openExportModal() {
            this.exportModalOpen = true;
        },
        
        async exportReport() {
            try {
                const params = new URLSearchParams();
                params.append('format', this.exportFormat);
                if (this.exportDateFrom) params.append('date_from', this.exportDateFrom);
                if (this.exportDateTo) params.append('date_to', this.exportDateTo);
                
                window.location.href = `{{ route('kehadiran.export') }}?${params.toString()}`;
                this.exportModalOpen = false;
            } catch (error) {
                alert('Gagal export laporan: ' + error.message);
            }
        },
        
        getStatusBadgeClass(status) {
            if (status === 'ONGOING') {
                return 'bg-green-100 text-green-700';
            } else if (status === 'UPCOMING') {
                return 'bg-amber-100 text-amber-700';
            } else if (status === 'ENDED') {
                return 'bg-gray-100 text-gray-600';
            }
            return 'bg-gray-100 text-gray-600';
        },
        
        formatDate(dateString) {
            return dateString;
        }
    };
}
</script>
@endsection
