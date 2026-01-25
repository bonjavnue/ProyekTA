@extends('layouts.admin')

@section('content')
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

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-brand-red/10 border border-brand-red/30 text-brand-red px-4 py-3 rounded-lg mb-6" role="alert">
            {{ session('error') }}
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
<div id="tambahModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden my-8">
        <div class="bg-gradient-to-r from-brand-blue to-blue-900 p-6 flex justify-between items-center text-white">
            <h3 class="font-bold uppercase tracking-wider">Tambah Admin Baru</h3>
            <button onclick="closeTambahModal()" class="hover:rotate-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="formTambah" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                <input 
                    type="email" 
                    name="email" 
                    placeholder="admin@company.com" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" 
                    required>
                <span class="error-email text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password *</label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Minimal 8 karakter" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" 
                    required>
                <span class="error-password text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password *</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Ulangi password" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" 
                    required>
                <span class="error-password_confirmation text-xs text-brand-red hidden"></span>
            </div>

            <div class="flex gap-3 pt-4">
                <button 
                    type="submit" 
                    class="flex-1 bg-brand-blue hover:bg-blue-900 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    Simpan
                </button>
                <button 
                    type="button" 
                    onclick="closeTambahModal()" 
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Admin -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden my-8">
        <div class="bg-gradient-to-r from-brand-blue to-blue-900 p-6 flex justify-between items-center text-white">
            <h3 class="font-bold uppercase tracking-wider">Edit Admin</h3>
            <button onclick="closeEditModal()" class="hover:rotate-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="formEdit" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                <input 
                    type="email" 
                    name="email" 
                    placeholder="admin@company.com" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" 
                    required>
                <span class="error-email text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password (Kosongkan jika tidak ingin mengubah)</label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Minimal 8 karakter" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none">
                <span class="error-password text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Ulangi password" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none">
                <span class="error-password_confirmation text-xs text-brand-red hidden"></span>
            </div>

            <div class="flex gap-3 pt-4">
                <button 
                    type="submit" 
                    class="flex-1 bg-brand-blue hover:bg-blue-900 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    Simpan Perubahan
                </button>
                <button 
                    type="button" 
                    onclick="closeEditModal()" 
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete Admin -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="bg-brand-red p-6 flex justify-between items-center text-white">
            <h3 class="font-bold uppercase tracking-wider">Hapus Admin</h3>
            <button onclick="closeDeleteModal()" class="hover:rotate-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6">
            <p class="text-gray-700 mb-6">
                Apakah Anda yakin ingin menghapus admin <strong id="deleteAdminEmail"></strong>? Tindakan ini tidak dapat dibatalkan.
            </p>
            
            <form id="formDelete" class="flex gap-3">
                @csrf
                @method('DELETE')
                
                <button 
                    type="submit" 
                    class="flex-1 bg-brand-red hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    Hapus
                </button>
                <button 
                    type="button" 
                    onclick="closeDeleteModal()" 
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                    Batal
                </button>
            </form>
        </div>
    </div>
</div>

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
                alert('Admin berhasil ditambahkan');
                closeTambahModal();
                location.reload();
            } else {
                showErrors(data.errors || {}, 'formTambah');
                if (data.message) {
                    alert(data.message);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
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
                alert('Admin berhasil diperbarui');
                closeEditModal();
                location.reload();
            } else {
                showErrors(data.errors || {}, 'formEdit');
                if (data.message) {
                    alert(data.message);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        }
    });

    document.getElementById('formDelete').addEventListener('submit', async function(e) {
        e.preventDefault();
        
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
                alert('Admin berhasil dihapus');
                closeDeleteModal();
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus admin');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
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
</script>
@endsection
