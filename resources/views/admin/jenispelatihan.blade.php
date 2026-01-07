@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Jenis Pelatihan</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola data pelatihan perusahaan.</p>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="relative flex-1 md:flex-none">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Cari..." 
                    value="{{ request('search', '') }}"
                    class="w-full md:w-64 bg-white text-gray-700 border border-gray-300 rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:border-brand-blue focus:ring-1 focus:ring-brand-blue shadow-sm transition-all"
                >
            </div>
            
            <button onclick="openModal()" class="flex items-center gap-2 bg-brand-blue hover:bg-blue-900 text-white font-medium py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Pelatihan</span>
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
                        <th class="p-4 cursor-pointer hover:text-brand-blue group transition-colors" onclick="sortColumn('id_jenis')">
                            ID Jenis 
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-id_jenis">↓</span>
                        </th>
                        <th class="p-4 cursor-pointer hover:text-brand-blue group transition-colors" onclick="sortColumn('nama_jenis')">
                            Nama Jenis Pelatihan
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-nama_jenis">↓</span>
                        </th>
                        <th class="p-4 cursor-pointer hover:text-brand-blue group transition-colors" onclick="sortColumn('created_at')">
                            Terakhir Diperbarui
                            <span class="inline-block ml-1 text-gray-300 group-hover:text-brand-blue" id="sort-created_at">↓</span>
                        </th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($jenisPelatihans as $pelatihan)
                    <tr class="hover:bg-blue-50/50 transition-colors duration-200 group" data-id="{{ $pelatihan->id_jenis }}" data-name="{{ $pelatihan->nama_jenis }}" data-desc="{{ $pelatihan->deskripsi }}">
                        <td class="p-4">
                            <input type="checkbox" class="rowCheckbox w-4 h-4 rounded border-gray-300 text-brand-blue focus:ring-brand-blue cursor-pointer" value="{{ $pelatihan->id_jenis }}">
                        </td>
                        <td class="p-4 font-semibold text-brand-blue">{{ $pelatihan->id_jenis }}</td>
                        <td class="p-4">
                            <span class="font-medium text-gray-800">{{ $pelatihan->nama_jenis }}</span>
                        </td>
                        <td class="p-4 text-gray-500">{{ $pelatihan->updated_at->format('M d, Y') }}</td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button 
                                    onclick="openDetailModal('{{ $pelatihan->id_jenis }}', '{{ $pelatihan->nama_jenis }}', '{{ addslashes($pelatihan->deskripsi) }}')"
                                    class="p-1.5 rounded-md hover:bg-blue-50 text-gray-400 hover:text-brand-blue transition-colors" title="Lihat">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                <button 
                                    onclick="openEditModal('{{ $pelatihan->id_jenis }}', '{{ $pelatihan->nama_jenis }}', '{{ addslashes($pelatihan->deskripsi) }}')"
                                    class="p-1.5 rounded-md hover:bg-yellow-50 text-gray-400 hover:text-brand-yellow transition-colors" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button 
                                    onclick="openDeleteModal('{{ $pelatihan->id_jenis }}', '{{ $pelatihan->nama_jenis }}')"
                                    class="p-1.5 rounded-md hover:bg-red-50 text-gray-400 hover:text-brand-red transition-colors" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            Tidak ada data jenis pelatihan. <a href="#" onclick="openModal()" class="text-brand-blue hover:underline">Tambah data baru</a>
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
                        Menampilkan <span class="font-bold text-gray-900">{{ $jenisPelatihans->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-gray-900">{{ $jenisPelatihans->lastItem() ?? 0 }}</span> dari <span class="font-bold text-gray-900">{{ $jenisPelatihans->total() }}</span> hasil
                    </div>
                    {{ $jenisPelatihans->links('pagination.custom') }}
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

<!-- Modal Tambah Pelatihan -->
<div id="tambahPelatihanModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full animate-fade-in">
        <div class="bg-gradient-to-r from-brand-blue to-blue-900 px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-bold text-white">Tambah Jenis Pelatihan</h2>
        </div>
        
        <form id="formTambahPelatihan" class="p-6">
            @csrf
            
            <div class="mb-4">
                <label for="nama_jenis" class="block text-sm font-medium text-gray-700 mb-2">Nama Pelatihan</label>
                <input 
                    type="text" 
                    id="nama_jenis" 
                    name="nama_jenis" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    placeholder="Contoh: Leadership & Management"
                    required
                >
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-6">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea 
                    id="deskripsi" 
                    name="deskripsi" 
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition resize-none"
                    placeholder="Masukkan deskripsi pelatihan..."
                    required
                ></textarea>
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="flex gap-3 justify-end">
                <button 
                    type="button" 
                    onclick="closeModal()"
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

<script>
    const modal = document.getElementById('tambahPelatihanModal');
    const form = document.getElementById('formTambahPelatihan');

    function openModal() {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        form.reset();
        clearErrors();
    }

    // Close modal saat klik di luar form
    // modal.addEventListener('click', function(e) {
    //     if (e.target === modal) {
    //         closeModal();
    //     }
    // });

    // Close modal saat tekan ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    function changePerPage(value) {
        // Ambil current URL dan tambahkan/update parameter per_page
        const url = new URL(window.location);
        url.searchParams.set('per_page', value);
        // Reset ke halaman 1 saat mengubah per_page
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
            
            // Update URL tanpa reload
            window.history.replaceState({}, '', href);
            
            // Scroll ke atas tabel
            document.querySelector('table').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => console.error('Error:', error));
    }

    // Handle pencarian dengan AJAX (tanpa reload halaman)
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(() => {
                const searchQuery = e.target.value.trim();
                const perPage = new URL(window.location).searchParams.get('per_page') || '10';
                
                // Fetch data dengan AJAX
                const url = new URL('/jenispelatihan', window.location.origin);
                
                // Jika search kosong, hapus parameter search
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
                    // Extract hanya tbody dan pagination dari HTML response
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
                    
                    // Update URL tanpa reload
                    window.history.replaceState({}, '', url.toString());
                })
                .catch(error => console.error('Error:', error));
            }, 300); // Debounce 300ms
        });
    }

    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }

    // Sorting functionality
    let currentSort = new URL(window.location).searchParams.get('sort_by') || 'created_at';
    let currentOrder = new URL(window.location).searchParams.get('sort_order') || 'desc';

    function updateSortIndicators() {
        // Reset semua indicators
        document.querySelectorAll('[id^="sort-"]').forEach(el => {
            el.textContent = '↓';
            el.classList.remove('text-brand-blue');
            el.classList.add('text-gray-300');
        });
        
        // Update current sort indicator
        const currentIndicator = document.getElementById(`sort-${currentSort}`);
        if (currentIndicator) {
            currentIndicator.textContent = currentOrder === 'asc' ? '↑' : '↓';
            currentIndicator.classList.remove('text-gray-300');
            currentIndicator.classList.add('text-brand-blue');
        }
    }

    function sortColumn(column) {
        // Jika sorting column yang sama, toggle order
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
        
        // Update URL dengan sort parameters
        url.searchParams.set('sort_by', currentSort);
        url.searchParams.set('sort_order', currentOrder);
        url.searchParams.set('per_page', perPage);
        
        if (searchQuery) {
            url.searchParams.set('search', searchQuery);
        } else {
            url.searchParams.delete('search');
        }
        
        // Reset ke page 1
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
            
            // Update URL tanpa reload
            window.history.replaceState({}, '', url.toString());
            
            // Update sort indicators
            updateSortIndicators();
            
            // Scroll ke atas tabel
            document.querySelector('table').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(error => console.error('Error:', error));
    }

    // Initialize sort indicators on page load
    updateSortIndicators();

    // Handle select all checkbox
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    let totalDataCount = {{ $jenisPelatihans->total() }}; // Total dari database
    let allDataSelected = false; // Flag untuk tracking select all database
    let allDataItems = []; // Simpan semua data dari database

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
                // Fetch semua data dari database
                try {
                    const perPage = new URL(window.location).searchParams.get('per_page') || '10';
                    const searchQuery = document.getElementById('searchInput').value.trim();
                    
                    const url = new URL('/jenispelatihan', window.location.origin);
                    url.searchParams.set('per_page', 999999); // Get semua data
                    
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
                    
                    // Extract semua rows dari response
                    allDataItems = [];
                    doc.querySelectorAll('tbody tr').forEach(row => {
                        const checkbox = row.querySelector('.rowCheckbox');
                        if (checkbox) {
                            allDataItems.push({
                                id: checkbox.value,
                                name: row.dataset.name,
                                desc: row.dataset.desc
                            });
                        }
                    });
                    
                    // Ceklis semua checkbox yang terlihat di halaman saat ini
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
                // Unchecklist semua
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
                name: row.dataset.name,
                desc: row.dataset.desc
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
            ? selected.slice(0, 5).map(item => item.name).join(', ') + ` ... (+${selected.length - 5} lainnya)`
            : selected.map(item => item.name).join(', ');
        
        if (confirm(`Yakin hapus ${selected.length} item?\n\n${itemsPreview}`)) {
            bulkDeleteItems(selected.map(item => item.id));
        }
    }

    async function bulkDeleteItems(ids) {
        try {
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            
            for (const id of ids) {
                const formData = new FormData();
                if (csrfToken) {
                    formData.append('_token', csrfToken);
                }
                
                await fetch(`/jenispelatihan/${id}/delete`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            }
            
            alert(`${ids.length} item berhasil dihapus`);
            location.reload();
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        }
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearErrors();

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menyimpan...</span>';

        const formData = new FormData(form);

        try {
            const response = await fetch('/jenispelatihan/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok) {
                alert('Jenis Pelatihan berhasil ditambahkan');
                closeModal();
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
</script>

<!-- Modal Edit Pelatihan -->
<div id="editPelatihanModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full animate-fade-in">
        <div class="bg-gradient-to-r from-brand-blue to-blue-900 px-6 py-4 rounded-t-lg">
            <h2 class="text-xl font-bold text-white">Edit Jenis Pelatihan</h2>
        </div>
        
        <form id="formEditPelatihan" class="p-6">
            @csrf
            
            <input type="hidden" id="edit_id_jenis" name="edit_id_jenis">
            
            <div class="mb-4">
                <label for="edit_nama_jenis" class="block text-sm font-medium text-gray-700 mb-2">Nama Pelatihan</label>
                <input 
                    type="text" 
                    id="edit_nama_jenis" 
                    name="nama_jenis" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                    placeholder="Contoh: Leadership & Management"
                    required
                >
                <span class="error-message text-red-500 text-sm mt-1 hidden"></span>
            </div>

            <div class="mb-6">
                <label for="edit_deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea 
                    id="edit_deskripsi" 
                    name="deskripsi" 
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition resize-none"
                    placeholder="Masukkan deskripsi pelatihan..."
                    required
                ></textarea>
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

<script>
    const editModal = document.getElementById('editPelatihanModal');
    const editForm = document.getElementById('formEditPelatihan');

    function openEditModal(idJenis, namaJenis, deskripsi) {
        document.getElementById('edit_id_jenis').value = idJenis;
        document.getElementById('edit_nama_jenis').value = namaJenis;
        document.getElementById('edit_deskripsi').value = deskripsi;
        editModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        editModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        editForm.reset();
        clearEditErrors();
    }

    // Close modal saat tekan ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !editModal.classList.contains('hidden')) {
            closeEditModal();
        }
    });

    function clearEditErrors() {
        editForm.querySelectorAll('.error-message').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
    }

    editForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        clearEditErrors();

        const idJenis = document.getElementById('edit_id_jenis').value;
        const editSubmitBtn = document.getElementById('editSubmitBtn');
        editSubmitBtn.disabled = true;
        editSubmitBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menyimpan...</span>';

        const formData = new FormData(editForm);

        try {
            const response = await fetch(`/jenispelatihan/${idJenis}/update`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok) {
                alert('Jenis Pelatihan berhasil diperbarui');
                closeEditModal();
                location.reload();
            } else {
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const selector = `#edit_${field}`;
                        const element = document.querySelector(selector);
                        if (element) {
                            const errorElement = element.parentElement.querySelector('.error-message');
                            if (errorElement) {
                                errorElement.textContent = data.errors[field][0];
                                errorElement.classList.remove('hidden');
                            }
                        }
                    });
                } else {
                    alert(data.message || 'Gagal menyimpan perubahan');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan perubahan');
        } finally {
            editSubmitBtn.disabled = false;
            editSubmitBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> <span>Simpan Perubahan</span>';
        }
    });
</script>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full animate-fade-in">
        <div class="bg-red-50 px-6 py-4 border-b border-red-200 rounded-t-lg">
            <h2 class="text-lg font-bold text-red-900">Hapus Jenis Pelatihan</h2>
        </div>
        
        <div class="p-6">
            <p class="text-gray-700 mb-2">
                Apakah Anda yakin ingin menghapus jenis pelatihan berikut?
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

<script>
    const deleteModal = document.getElementById('deleteConfirmModal');
    let deleteIdJenis = null;

    function openDeleteModal(idJenis, namaJenis) {
        deleteIdJenis = idJenis;
        document.getElementById('deleteItemName').textContent = namaJenis;
        deleteModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        deleteModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        deleteIdJenis = null;
    }

    // Close modal saat tekan ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
            closeDeleteModal();
        }
    });

    async function confirmDelete() {
        const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
        deleteConfirmBtn.disabled = true;
        deleteConfirmBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menghapus...</span>';

        try {
            // Gunakan FormData dengan CSRF token dari form
            const formData = new FormData();
            const csrfToken = document.querySelector('input[name="_token"]')?.value;
            if (csrfToken) {
                formData.append('_token', csrfToken);
            }
            
            const response = await fetch(`/jenispelatihan/${deleteIdJenis}/delete`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (response.ok) {
                alert('Jenis Pelatihan berhasil dihapus');
                closeDeleteModal();
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus data');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        } finally {
            deleteConfirmBtn.disabled = false;
            deleteConfirmBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Ya, Hapus</span>';
        }
    }
</script>

<!-- Modal Detail Pelatihan -->
<div id="detailPelatihanModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full animate-fade-in">
        <div class="bg-gradient-to-r from-brand-blue to-blue-900 px-6 py-4 rounded-t-lg flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">Detail Jenis Pelatihan</h2>
            <!-- <button 
                onclick="closeDetailModal()"
                class="text-white hover:text-gray-200 transition"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button> -->
        </div>
        
        <div class="p-6">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">ID Jenis Pelatihan</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-800 font-semibold" id="detail_id_jenis">-</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pelatihan</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-800 font-semibold" id="detail_nama_jenis">-</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <div class="px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 max-h-40 overflow-y-auto">
                    <p class="text-gray-800 whitespace-pre-wrap" id="detail_deskripsi">-</p>
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

<script>
    const detailModal = document.getElementById('detailPelatihanModal');

    function openDetailModal(idJenis, namaJenis, deskripsi) {
        document.getElementById('detail_id_jenis').textContent = idJenis;
        document.getElementById('detail_nama_jenis').textContent = namaJenis;
        document.getElementById('detail_deskripsi').textContent = deskripsi || '-';
        detailModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDetailModal() {
        detailModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modal saat tekan ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !detailModal.classList.contains('hidden')) {
            closeDetailModal();
        }
    });

    // Close modal saat klik di luar form
    // detailModal.addEventListener('click', function(e) {
    //     if (e.target === detailModal) {
    //         closeDetailModal();
    //     }
    // });
</script>
@endsection