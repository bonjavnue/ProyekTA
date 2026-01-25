@extends('layouts.admin')

@section('content')
<div class="container mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kelola Supervisor</h1>
            <p class="text-gray-500 text-sm mt-1">Manajemen akun supervisor</p>
        </div>
        <button onclick="openTambahModal()" class="flex items-center gap-2 bg-brand-blue hover:bg-blue-900 text-white font-medium py-2 px-4 rounded-lg shadow-md transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Supervisor</span>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <!-- <div class="mb-6 pb-4 border-b-2 border-gray-200 flex gap-4">
        <a href="{{ route('supervisor.index') }}" class="pb-3 font-semibold text-gray-700 border-b-2 border-brand-blue transition-colors">
            Kelola Supervisor
        </a>
        <a href="{{ route('bagian.index') }}" class="pb-3 font-semibold text-gray-500 hover:text-brand-blue border-b-2 border-transparent hover:border-brand-blue transition-colors">
            Kelola Bagian
        </a>
    </div> -->

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

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        @if($supervisors->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <p class="text-gray-500 text-lg font-medium">Belum ada data supervisor</p>
                <p class="text-gray-400 text-sm mt-1">Mulai dengan menambahkan supervisor baru</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <!-- <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Nama Supervisor</th> -->
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700">Bagian yang Ditangani</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($supervisors as $supervisor)
                        <tr class="hover:bg-gray-50 transition">
                            <!-- <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $supervisor->name }}</td> -->
                            <td class="px-6 py-3 text-sm text-gray-700">{{ $supervisor->email }}</td>
                            <td class="px-6 py-3 text-sm">
                                <div class="flex flex-wrap gap-2">
                                    @if($supervisor->bagian)
                                        <span class="inline-block px-3 py-1 bg-brand-yellow/20 text-yellow-800 text-xs font-medium rounded border border-brand-yellow/30">{{ $supervisor->bagian->nama_bagian }}</span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded border border-gray-300">Belum ada bagian</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal({{ $supervisor->id }})" class="text-amber-600 hover:text-amber-800 font-medium" title="Edit">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button onclick="openDeleteModal({{ $supervisor->id }}, '{{ $supervisor->name }}')" class="text-red-600 hover:text-red-800 font-medium" title="Hapus">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<!-- Modal Tambah Supervisor -->
<div id="tambahModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden my-8">
        <div class="bg-brand-blue p-4 flex justify-between items-center text-white">
            <h3 class="font-bold uppercase tracking-wider">Tambah Supervisor Baru</h3>
            <button onclick="closeTambahModal()" class="hover:rotate-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="formTambah" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" placeholder="Contoh: supervisor@company.com" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" required>
                <span class="error-email text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password *</label>
                <input type="password" name="password" placeholder="Minimal 8 karakter" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" required>
                <span class="error-password text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password *</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" required>
                <span class="error-password_confirmation text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Bagian *</label>
                <input type="text" name="nama_bagian" placeholder="Contoh: IT Production, Human Resources" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" required>
                <span class="error-nama_bagian text-xs text-brand-red hidden"></span>
                <p class="text-xs text-gray-500 mt-1">Satu supervisor hanya menangani satu bagian</p>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-brand-blue hover:bg-blue-900 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    Simpan
                </button>
                <button type="button" onclick="closeTambahModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Supervisor -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full overflow-hidden my-8">
        <div class="bg-brand-blue p-4 flex justify-between items-center text-white">
            <h3 class="font-bold uppercase tracking-wider">Edit Supervisor</h3>
            <button onclick="closeEditModal()" class="hover:rotate-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form id="formEdit" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" placeholder="Contoh: supervisor@company.com" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" required>
                <span class="error-email text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" placeholder="Minimal 8 karakter" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none">
                <span class="error-password text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none">
                <span class="error-password_confirmation text-xs text-brand-red hidden"></span>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Bagian *</label>
                <input type="text" name="nama_bagian" placeholder="Contoh: IT Production, Human Resources" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-1 focus:ring-brand-blue outline-none" required>
                <span class="error-nama_bagian text-xs text-brand-red hidden"></span>
                <p class="text-xs text-gray-500 mt-1">Satu supervisor hanya menangani satu bagian</p>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-brand-blue hover:bg-blue-900 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    Simpan Perubahan
                </button>
                <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete Supervisor -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="bg-brand-red p-4 flex justify-between items-center text-white">
            <h3 class="font-bold uppercase tracking-wider">Hapus Supervisor</h3>
            <button onclick="closeDeleteModal()" class="hover:rotate-90 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6">
            <p class="text-gray-700 mb-6">
                Apakah Anda yakin ingin menghapus supervisor <strong id="deleteSupervisorName"></strong>? Tindakan ini tidak dapat dibatalkan.
            </p>
            
            <form id="formDelete" class="flex gap-3">
                @csrf
                @method('DELETE')
                
                <button type="submit" class="flex-1 bg-brand-red hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    Hapus
                </button>
                <button type="button" onclick="closeDeleteModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors">
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
    let editSupervisorId = null;
    let deleteSupervisorId = null;

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

    function openEditModal(id) {
        fetch(`/admin/kelolasupervisor/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const user = data.user;
                    editSupervisorId = user.id;
                    
                    document.querySelector('#formEdit input[name="email"]').value = user.email;
                    document.querySelector('#formEdit input[name="nama_bagian"]').value = user.bagian?.nama_bagian || '';
                    
                    document.getElementById('editModal').classList.remove('hidden');
                    clearErrors('formEdit');
                }
            })
            .catch(err => console.error('Error:', err));
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('formEdit').reset();
        clearErrors('formEdit');
        editSupervisorId = null;
    }

    function openDeleteModal(id, name) {
        deleteSupervisorId = id;
        document.getElementById('deleteSupervisorName').textContent = name;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteSupervisorId = null;
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
            password_confirmation: formData.get('password_confirmation'),
            nama_bagian: formData.get('nama_bagian')
        };

        try {
            const res = await fetch('/admin/kelolasupervisor/store', {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(body)
            });

            const data = await res.json();

            if (data.success) {
                alert('Supervisor berhasil ditambahkan');
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
            email: formData.get('email'),
            nama_bagian: formData.get('nama_bagian')
        };

        // Only add password if provided
        if (formData.get('password')) {
            body.password = formData.get('password');
            body.password_confirmation = formData.get('password_confirmation');
        }

        try {
            const res = await fetch(`/admin/kelolasupervisor/${editSupervisorId}/update`, {
                method: 'PUT',
                headers: headers,
                body: JSON.stringify(body)
            });

            const data = await res.json();

            if (data.success) {
                alert('Supervisor berhasil diperbarui');
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
            const res = await fetch(`/admin/kelolasupervisor/${deleteSupervisorId}/delete`, {
                method: 'DELETE',
                headers: headers
            });

            const data = await res.json();

            if (data.success) {
                alert('Supervisor berhasil dihapus');
                closeDeleteModal();
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus supervisor');
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