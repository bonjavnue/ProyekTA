@extends('layouts.admin')

@section('content')

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3 max-w-md"></div>

<!-- Confirm Modal -->
<div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full animate-fade-in">
        <div class="bg-blue-50 px-6 py-4 border-b border-blue-200 rounded-t-lg">
            <h2 class="text-lg font-bold text-blue-900" id="confirmTitle">Konfirmasi</h2>
        </div>
        
        <div class="p-6">
            <p class="text-gray-700 mb-6" id="confirmMessage">Apakah Anda yakin?</p>

            <div class="flex gap-3 justify-end">
                <button 
                    type="button" 
                    onclick="closeConfirmModal()"
                    class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium"
                >
                    Batal
                </button>
                <button 
                    type="button" 
                    onclick="executeConfirm()"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium flex items-center gap-2"
                    id="confirmBtn"
                >
                    <span id="confirmBtnText">Ya</span>
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

<div class="container mx-auto" x-data="kehadiranDetailApp()">
    
    <!-- Header dengan Back Button -->
    <div class="mb-6 flex items-center gap-4">
        <!-- <a href="{{ route('supervisor.kehadiran.index') }}" class="p-2 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a> -->
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $jadwal->JenisPelatihan->nama_jenis }}</h1>
            <p class="text-sm text-gray-500">
                {{ $jadwal->tanggal_pelaksanaan->format('d M Y') }} | 
                {{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }} |
                {{ $jadwal->tempat }}
            </p>
        </div>
    </div>

    <!-- Search Bar with Buttons -->
    <div class="mb-6 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
        <div class="flex flex-col sm:flex-row gap-3 flex-1">
            <input type="text" 
                   x-model="searchKaryawan" 
                   placeholder="Cari nama atau NIK karyawan..." 
                   class="px-4 py-2 border border-gray-200 rounded-lg text-sm flex-1 outline-none focus:ring-1 focus:ring-brand-blue">
            
            <button @click="selectAllPresent()" 
                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium text-sm transition-all shadow-sm whitespace-nowrap flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Hadir Semua
            </button>
            
            <button @click="resetAllStatus()" 
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium text-sm transition-all shadow-sm whitespace-nowrap flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset
            </button>
        </div>

        <!-- Export Dropdown -->
        <div class="relative group">
            <!-- <button type="button" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-all shadow-sm flex items-center justify-center gap-2 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Export
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </button> -->
            
            <!-- Dropdown Menu -->
            <div class="absolute right-0 mt-0 w-40 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
                <a href="{{ route('supervisor.kehadiran.export-pdf', $jadwal->id_jadwal) }}" 
                   class="block px-4 py-3 text-gray-700 hover:bg-gray-100 first:rounded-t-lg font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('supervisor.kehadiran.export-excel', ['id' => $jadwal->id_jadwal, 'format' => 'csv']) }}" 
                   class="block px-4 py-3 text-gray-700 hover:bg-gray-100 font-medium text-sm border-t border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </a>
                <a href="{{ route('supervisor.kehadiran.export-excel', ['id' => $jadwal->id_jadwal, 'format' => 'xls']) }}" 
                   class="block px-4 py-3 text-gray-700 hover:bg-gray-100 last:rounded-b-lg font-medium text-sm border-t border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export XLS
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Kehadiran -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Karyawan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Bagian</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Waktu Presensi</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <template x-for="karyawan in paginatedKaryawans" :key="karyawan.id_karyawan">
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 text-sm">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-blue text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                        <span x-text="karyawan.nama_karyawan.substring(0, 2).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900" x-text="karyawan.nama_karyawan"></p>
                                        <p class="text-xs text-gray-500" x-text="'NIK: ' + karyawan.nik"></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700" x-text="karyawan.bagian_nama"></td>
                            <td class="px-6 py-3 text-sm text-gray-700">
                                <span x-show="karyawan.waktu_presensi" x-text="formatDate(karyawan.waktu_presensi)"></span>
                                <span x-show="!karyawan.waktu_presensi" class="text-gray-400 italic">-</span>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-700">
                                <span x-show="karyawan.waktu_presensi" x-text="formatTime(karyawan.waktu_presensi)"></span>
                                <span x-show="!karyawan.waktu_presensi" class="text-gray-400 italic">-</span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <select @change="updateKaryawanStatus(karyawan.id_karyawan, $event.target.value)"
                                        :value="karyawan.status"
                                        class="text-xs font-bold uppercase bg-white border border-gray-200 rounded px-2 py-1 outline-none focus:ring-1 focus:ring-brand-blue transition-all"
                                        :class="statusClass(karyawan.status)">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Alpa">Alpa</option>
                                    <option value="Belum Presensi">Belum Presensi</option>
                                </select>
                            </td>
                            <td class="px-6 py-3 text-center text-sm">
                                <span x-show="karyawan.dicatat_oleh === 'admin'" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded">Admin</span>
                                <span x-show="karyawan.dicatat_oleh === 'supervisor'" class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded">Supervisor</span>
                                <span x-show="karyawan.dicatat_oleh === 'karyawan'" class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded">Karyawan</span>
                                <span x-show="!karyawan.dicatat_oleh" class="text-gray-400 italic text-xs">-</span>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="paginatedKaryawans.length === 0 && filteredKaryawans.length === 0">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-gray-500">Tidak ada karyawan yang sesuai dengan pencarian</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="bg-gray-50 border-t border-gray-200 px-4 py-4 sm:px-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Pagination Info -->
                <div class="text-sm text-gray-600">
                    Menampilkan <span class="font-bold text-gray-900" x-text="getPaginationStart()"></span> sampai <span class="font-bold text-gray-900" x-text="getPaginationEnd()"></span> dari <span class="font-bold text-gray-900" x-text="filteredKaryawans.length"></span> hasil
                </div>

                <!-- Per Page Dropdown -->
                <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 whitespace-nowrap">Tampilkan per halaman</label>
                    <select @change="perPage = parseInt($event.target.value); currentPage = 1" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue text-sm bg-white font-medium">
                        <option value="10" :selected="perPage == 10">10</option>
                        <option value="25" :selected="perPage == 25">25</option>
                        <option value="50" :selected="perPage == 50">50</option>
                        <option value="100" :selected="perPage == 100">100</option>
                    </select>
                </div>
            </div>

            <!-- Pagination Links -->
            <div class="mt-4" x-show="totalPages > 1">
                <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between sm:justify-start gap-2">
                    {{-- Previous Page Link --}}
                    <button @click="previousPage()" 
                            :disabled="currentPage === 1"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                            :class="currentPage === 1 ? 'text-gray-500 cursor-not-allowed opacity-50' : ''">
                        ←
                    </button>

                    {{-- Pagination Elements --}}
                    <div class="hidden sm:flex items-center gap-1">
                        <template x-for="page in totalPages" :key="page">
                            <button @click="currentPage = page"
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                                    :class="currentPage === page ? 'text-white bg-brand-blue border border-brand-blue font-bold' : ''">
                                <span x-text="page"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Next Page Link --}}
                    <button @click="nextPage()" 
                            :disabled="currentPage === totalPages"
                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                            :class="currentPage === totalPages ? 'text-gray-500 cursor-not-allowed opacity-50' : ''">
                        →
                    </button>

                    <!-- Page info for mobile -->
                    <div class="sm:hidden ml-auto text-sm text-gray-700">
                        Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages"></span>
                    </div>
                </nav>
            </div>
        </div>

<script>
function kehadiranDetailApp() {
    return {
        karyawans: @json($karyawans),
        searchKaryawan: '',
        jadwalId: {{ $jadwal->id_jadwal }},
        perPage: {{ $perPage ?? 10 }},
        currentPage: 1,
        
        get filteredKaryawans() {
            return this.karyawans.filter(k => 
                k.nama_karyawan.toLowerCase().includes(this.searchKaryawan.toLowerCase()) ||
                k.nik.includes(this.searchKaryawan)
            );
        },
        
        get totalPages() {
            return Math.ceil(this.filteredKaryawans.length / this.perPage) || 1;
        },
        
        get paginatedKaryawans() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.filteredKaryawans.slice(start, end);
        },
        
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        
        async updateKaryawanStatus(idKaryawan, newStatus) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    showToast('CSRF token tidak ditemukan', 'error');
                    return;
                }

                const response = await fetch(`/supervisor/kehadiran/${this.jadwalId}/${idKaryawan}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.content
                    },
                    body: JSON.stringify({ 
                        status: newStatus,
                        _method: 'PUT'
                    })
                });
                
                const data = await response.json().catch(() => null);
                
                if (!response.ok) {
                    console.error('Server error:', response.status, data);
                    showToast('Error ' + response.status + ': ' + (data?.message || 'Gagal mengupdate status'), 'error');
                    return;
                }

                if (data && data.success) {
                    // Update karyawan di array
                    const idx = this.karyawans.findIndex(k => k.id_karyawan == idKaryawan);
                    if (idx >= 0) {
                        this.karyawans[idx].status = newStatus;
                        
                        if (newStatus === 'Belum Presensi') {
                            this.karyawans[idx].waktu_presensi = null;
                            this.karyawans[idx].dicatat_oleh = null;
                        } else {
                            this.karyawans[idx].dicatat_oleh = data.dicatat_oleh || null;
                            if (data.waktu_presensi) {
                                this.karyawans[idx].waktu_presensi = data.waktu_presensi;
                            }
                        }
                    }
                    console.log('Status berhasil diupdate');
                } else {
                    showToast('Response tidak valid dari server', 'error');
                }
            } catch (err) {
                console.error('Fetch error:', err);
                showToast('Gagal mengupdate status kehadiran: ' + err.message, 'error');
            }
        },
        
        statusClass(status) {
            const baseClass = 'border-0';
            if (status === 'Hadir') return baseClass + ' text-green-700 bg-green-50';
            if (status === 'Sakit') return baseClass + ' text-yellow-700 bg-yellow-50';
            if (status === 'Izin') return baseClass + ' text-blue-700 bg-blue-50';
            if (status === 'Alpa') return baseClass + ' text-red-700 bg-red-50';
            return baseClass + ' text-gray-700 bg-gray-50';
        },
        
        formatTime(timestamp) {
            if (!timestamp) return '--:--';
            const date = new Date(timestamp);
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${hours}:${minutes}`;
        },
        
        formatDate(timestamp) {
            if (!timestamp) return '--/--/--';
            const date = new Date(timestamp);
            return date.toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }).split('/').reverse().join('-');
        },
        
        getPaginationStart() {
            if (this.filteredKaryawans.length === 0) return 0;
            return (this.currentPage - 1) * this.perPage + 1;
        },
        
        getPaginationEnd() {
            return Math.min(this.currentPage * this.perPage, this.filteredKaryawans.length);
        },
        
        async selectAllPresent() {
            showConfirmModal(
                'Ubah Status Semua Karyawan?',
                'Apakah Anda yakin ingin mengubah status semua karyawan menjadi Hadir?',
                'Ya, Ubah',
                async () => {
                    for (const karyawan of this.karyawans) {
                        await this.updateKaryawanStatus(karyawan.id_karyawan, 'Hadir');
                    }
                    showToast('Semua karyawan berhasil diubah menjadi Hadir', 'success');
                }
            );
        },
        
        async resetAllStatus() {
            showConfirmModal(
                'Reset Status Semua Karyawan?',
                'Apakah Anda yakin ingin mereset status semua karyawan menjadi Belum Presensi?',
                'Ya, Reset',
                async () => {
                    for (const karyawan of this.karyawans) {
                        await this.updateKaryawanStatus(karyawan.id_karyawan, 'Belum Presensi');
                    }
                    showToast('Semua status berhasil direset menjadi Belum Presensi', 'success');
                }
            );
        }
    }
}

// ==================== CONFIRM MODAL FUNCTIONS ====================

let confirmCallback = null;

function showConfirmModal(title, message, btnText, callback) {
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmBtnText').textContent = btnText;
    confirmCallback = callback;
    document.getElementById('confirmModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    confirmCallback = null;
}

function executeConfirm() {
    if (confirmCallback) {
        confirmCallback();
    }
    closeConfirmModal();
}

// Close modal with ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('confirmModal').classList.contains('hidden')) {
            closeConfirmModal();
        }
    }
});

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

// ==================== TOAST FUNCTIONS ====================

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    
    if (!container) {
        console.error('Toast container not found!');
        return;
    }
    
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
