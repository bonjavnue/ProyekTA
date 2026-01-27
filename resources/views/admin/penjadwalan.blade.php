@extends('layouts.admin')

@section('content')

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3 max-w-md"></div>

<!-- Delete Confirm Modal -->
<div id="deleteConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full animate-fade-in">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
            <h2 class="text-lg font-bold text-black-900">Hapus Jadwal Pelatihan</h2>
        </div>
        
        <div class="p-6">
            <p class="text-gray-700 mb-2">
                Apakah Anda yakin ingin menghapus jadwal berikut?
            </p>
            <div class="mb-6 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-lg font-semibold text-gray-900 mb-2">
                    <span id="deleteItemName"></span>
                </p>
                <p class="text-sm text-gray-600">
                    <span id="deleteItemDate"></span>
                </p>
                <p class="text-sm text-gray-600">
                    <span id="deleteItemTime"></span>
                </p>
            </div>
            <p class="text-sm text-gray-600 mb-6">
                <svg class="w-5 h-5 inline text-amber-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                Data yang dihapus tidak dapat dipulihkan
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
                    onclick="confirmDeleteJadwal()"
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session('success') }}', 'success');
            });
        </script>
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
                                    <button @click="deleteConfirm(jadwal)" class="text-red-600 hover:text-red-800 font-medium" title="Hapus">
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
        
        deleteConfirm(jadwal) {
            const date = new Date(jadwal.tanggal_pelaksanaan);
            const formattedDate = date.toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' });
            deleteIdJadwal = jadwal.id_jadwal;
            document.getElementById('deleteItemName').textContent = jadwal.nama_jenis;
            document.getElementById('deleteItemDate').textContent = formattedDate;
            document.getElementById('deleteItemTime').textContent = `Jam: ${jadwal.jam_mulai} - ${jadwal.jam_selesai}`;
            document.getElementById('deleteConfirmModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };
}

// ==================== TOAST FUNCTIONS ====================

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    
    const toastId = 'toast-' + Date.now();
    let bgColor = 'bg-green-50 border-green-200';
    let textColor = 'text-green-800';
    let icon = '✓';
    let iconColor = 'text-green-500';
    
    if (type === 'error') {
        bgColor = 'bg-red-50 border-red-200';
        textColor = 'text-red-800';
        icon = '✕';
        iconColor = 'text-red-500';
    } else if (type === 'warning') {
        bgColor = 'bg-yellow-50 border-yellow-200';
        textColor = 'text-yellow-800';
        icon = '!';
        iconColor = 'text-yellow-500';
    } else if (type === 'info') {
        bgColor = 'bg-blue-50 border-blue-200';
        textColor = 'text-blue-800';
        icon = 'i';
        iconColor = 'text-blue-500';
    }
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${bgColor} border rounded-lg p-4 shadow-md flex items-start gap-3 animate-slide-in`;
    toast.innerHTML = `
        <div class="flex-shrink-0 font-bold ${iconColor}">${icon}</div>
        <div class="flex-1 ${textColor} text-sm">${message}</div>
        ${type !== 'success' ? `<button onclick="removeToast('${toastId}')" class="${textColor} hover:opacity-70 transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </button>` : ''}
    `;
    
    container.appendChild(toast);
    
    // Auto-remove success toast after 3 seconds
    if (type === 'success') {
        setTimeout(() => {
            removeToast(toastId);
        }, 3000);
    }
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('animate-slide-out');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}

// ==================== DELETE MODAL FUNCTIONS ====================

let deleteIdJadwal = null;

function closeDeleteModal() {
    document.getElementById('deleteConfirmModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    deleteIdJadwal = null;
}

async function confirmDeleteJadwal() {
    const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
    deleteConfirmBtn.disabled = true;
    deleteConfirmBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menghapus...</span>';

    try {
        const csrfToken = document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.content;
        
        const response = await fetch(`/admin/penjadwalan/${deleteIdJadwal}`, {
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
            showToast('Jadwal berhasil dihapus', 'success');
            closeDeleteModal();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Gagal menghapus jadwal', 'error');
            deleteConfirmBtn.disabled = false;
            deleteConfirmBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Ya, Hapus</span>';
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menghapus jadwal: ' + error.message, 'error');
        deleteConfirmBtn.disabled = false;
        deleteConfirmBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Ya, Hapus</span>';
    }
}

// Close modal with ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('deleteConfirmModal').classList.contains('hidden')) {
            closeDeleteModal();
        }
    }
});

// Close modal when clicking outside
document.getElementById('deleteConfirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// ==================== ADD STYLE FOR ANIMATIONS ====================

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(100%);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }
    
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }
    
    .animate-slide-out {
        animation: slideOut 0.3s ease-out;
    }
`;
document.head.appendChild(style);
</script>
@endsection