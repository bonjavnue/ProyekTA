@extends('layouts.admin')

@section('content')

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-3 max-w-md"></div>

<div class="container mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Admin</h1>
            <p class="text-gray-500 text-sm mt-1">Manajemen akun administrator sistem.</p>
        </div>
        <button onclick="openTambahModal()" class="flex items-center gap-2 bg-brand-blue hover:bg-blue-900 text-white font-medium py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Admin</span>
        </button>
    </div>

    <!-- Display Messages -->
    @if($errors->any())
        <div class="bg-brand-red/10 border border-brand-red/30 text-brand-red px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Dibuat</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($admins as $admin)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-3 text-sm font-semibold text-brand-blue">{{ $admin->email }}</td>
                        <td class="px-6 py-3 text-sm">
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">Aktif</span>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $admin->created_at ? $admin->created_at->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-3 text-center text-sm">
                            <div class="flex items-center justify-center gap-2">
                                <button 
                                    onclick="openEditModal('{{ $admin->email }}')"
                                    class="text-amber-600 hover:text-amber-800 font-medium"
                                    title="Edit">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button 
                                    onclick="openDeleteModal('{{ $admin->email }}')"
                                    class="text-red-600 hover:text-red-800 font-medium"
                                    title="Hapus">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-gray-500">Tidak ada data admin</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div id="tambahModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 modal-content max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Tambah Admin Baru</h2>
                <p class="text-sm text-gray-500 mt-1">Buat akun administrator baru untuk sistem</p>
            </div>
            <button onclick="closeTambahModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="formTambah" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Email *</label>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="admin@company.com" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" 
                        required>
                    <span class="error-email text-sm text-brand-red hidden mt-2 block"></span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Password *</label>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Minimal 8 karakter" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" 
                        required>
                    <span class="error-password text-sm text-brand-red hidden mt-2 block"></span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Konfirmasi Password *</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        placeholder="Ulangi password" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" 
                        required>
                    <span class="error-password_confirmation text-sm text-brand-red hidden mt-2 block"></span>
                </div>
            </div>

            <div class="flex gap-3 justify-end pt-6 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="closeTambahModal()"
                    class="px-6 py-2.5 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium text-sm"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 bg-brand-blue hover:bg-blue-900 text-white rounded-lg transition font-medium text-sm flex items-center gap-2 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Admin -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 modal-content max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between sticky top-0 bg-white">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Admin</h2>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi akun administrator</p>
            </div>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="formEdit" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Email *</label>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="admin@company.com" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition" 
                        required>
                    <span class="error-email text-sm text-brand-red hidden mt-2 block"></span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Password <span class="text-xs text-gray-500 font-normal">(Kosongkan jika tidak ingin mengubah)</span></label>
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Minimal 8 karakter" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition">
                    <span class="error-password text-sm text-brand-red hidden mt-2 block"></span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Konfirmasi Password</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        placeholder="Ulangi password" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition">
                    <span class="error-password_confirmation text-sm text-brand-red hidden mt-2 block"></span>
                </div>
            </div>

            <div class="flex gap-3 justify-end pt-6 border-t border-gray-200">
                <button 
                    type="button" 
                    onclick="closeEditModal()"
                    class="px-6 py-2.5 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium text-sm"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 bg-brand-blue hover:bg-blue-900 text-white rounded-lg transition font-medium text-sm flex items-center gap-2 shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete Admin -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full animate-fade-in">
        <div class="bg-white-50 px-6 py-4 border-b border-gray-200 rounded-t-lg">
            <h2 class="text-lg font-bold text-black-900">Hapus Admin</h2>
        </div>
        
        <div class="p-6">
            <p class="text-gray-700 mb-2">
                Apakah Anda yakin ingin menghapus admin berikut?
            </p>
            <p class="text-lg font-semibold text-gray-900 mb-6 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <span id="deleteAdminEmail"></span>
            </p>
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
                    type="submit"
                    form="formDelete"
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

<style>
    [class^="error-"] {
        display: none;
    }

    [class^="error-"].show {
        display: block;
    }
</style>

<script>
    let editAdminEmail = null;
    let deleteAdminEmail = null;

    // Modal Functions
    function openTambahModal() {
        document.getElementById('tambahModal').classList.remove('hidden');
        document.getElementById('formTambah').reset();
        clearErrors('formTambah');
    }

    function closeTambahModal() {
        document.getElementById('tambahModal').classList.add('hidden');
        document.getElementById('formTambah').reset();
        clearErrors('formTambah');
    }

    function openEditModal(email) {
        editAdminEmail = email;
        document.querySelector('#formEdit input[name="email"]').value = email;
        document.querySelector('#formEdit input[name="password"]').value = '';
        document.querySelector('#formEdit input[name="password_confirmation"]').value = '';
        document.getElementById('editModal').classList.remove('hidden');
        clearErrors('formEdit');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('formEdit').reset();
        clearErrors('formEdit');
        editAdminEmail = null;
    }

    function openDeleteModal(email) {
        deleteAdminEmail = email;
        document.getElementById('deleteAdminEmail').textContent = email;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteAdminEmail = null;
    }

    // Form Handlers
    document.getElementById('formTambah').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const headers = {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        const body = {
            email: formData.get('email'),
            password: formData.get('password'),
            password_confirmation: formData.get('password_confirmation')
        };

        try {
            const res = await fetch('/admin/kelolaadmin/store', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(body)
            });

            const data = await res.json();

            if (data.success) {
                showToast('Admin berhasil ditambahkan', 'success');
                closeTambahModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrors(data.errors || {}, 'formTambah');
                if (data.message) {
                    showToast(data.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'error');
        }
    });

    document.getElementById('formEdit').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const headers = {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        const body = {
            email: formData.get('email')
        };

        // Only add password if provided
        if (formData.get('password')) {
            body.password = formData.get('password');
            body.password_confirmation = formData.get('password_confirmation');
        }

        try {
            const res = await fetch(`/admin/kelolaadmin/${editAdminEmail}/update`, {
                method: 'PUT',
                headers: headers,
                body: JSON.stringify(body)
            });

            const data = await res.json();

            if (data.success) {
                showToast('Admin berhasil diperbarui', 'success');
                closeEditModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showErrors(data.errors || {}, 'formEdit');
                if (data.message) {
                    showToast(data.message, 'error');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'error');
        }
    });

    document.getElementById('formDelete').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const deleteBtn = document.getElementById('deleteConfirmBtn');
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menghapus...</span>';
        
        const headers = {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        try {
            const res = await fetch(`/admin/kelolaadmin/${deleteAdminEmail}/delete`, {
                method: 'DELETE',
                headers: headers
            });

            const data = await res.json();

            if (data.success) {
                showToast('Admin berhasil dihapus', 'success');
                closeDeleteModal();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || 'Gagal menghapus admin', 'error');
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Ya, Hapus</span>';
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan: ' + error.message, 'error');
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> <span>Ya, Hapus</span>';
        }
    });

    // Error Handling
    function clearErrors(formId) {
        const form = document.getElementById(formId);
        form.querySelectorAll('[class^="error-"]').forEach(el => {
            el.classList.remove('show');
            el.textContent = '';
        });
    }

    function showErrors(errors, formId) {
        clearErrors(formId);
        for (let field in errors) {
            const errorEl = document.querySelector(`#${formId} .error-${field}`);
            if (errorEl) {
                errorEl.textContent = errors[field][0];
                errorEl.classList.add('show');
            }
        }
    }

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

    // ==================== TOAST ANIMATIONS ====================

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
