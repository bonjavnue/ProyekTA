<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daftar Jenis Pelatihan - Filament</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'gray-900': '#111827',
            'gray-800': '#1f2937',
            'gray-700': '#374151',
            'gray-600': '#4b5563',
            'gray-400': '#9ca3af',
            'gray-300': '#d1d5db',
            'blue-600': '#2563eb',
            'blue-500': '#3b82f6',
          }
        }
      }
    }
  </script>
  <style>
    body { background:#111827; color:#d1d5db; font-family:system-ui,-apple-system,sans-serif; }
    ::-webkit-scrollbar { width:8px; }
    ::-webkit-scrollbar-track { background:#1f2937; }
    ::-webkit-scrollbar-thumb { background:#4b5563; border-radius:4px; }
    ::-webkit-scrollbar-thumb:hover { background:#6b7280; }
  </style>
</head>
<body class="flex h-screen overflow-hidden">

  <!-- SIDEBAR -->
  <aside class="w-64 bg-gray-800 flex flex-col">
    <div class="h-16 flex items-center px-6">
      <h1 class="text-2xl font-bold text-white">filament</h1>
    </div>
    <nav class="flex-1 px-4 py-4 space-y-2">
      <a href="#" class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path></svg>
        Dashboard
      </a>
      <a href="#" class="flex items-center px-4 py-2 rounded-lg bg-gray-900 text-white">
        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path></svg>
        Jenis Pelatihan
      </a>
    </nav>
  </aside>

  <!-- MAIN -->
  <div class="flex-1 flex flex-col overflow-hidden">
    <!-- HEADER -->
    <header class="h-16 flex items-center justify-between px-8 border-b border-gray-800 bg-gray-900">
      <div class="flex items-center">
        <div class="relative">
          <svg class="w-5 h-5 text-gray-400 absolute top-1/2 left-3 -translate-y-1/2" fill="currentColor" viewBox="0 0 20 20">
            <path clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" fill-rule="evenodd"></path>
          </svg>
          <input type="text" placeholder="Search..." class="bg-gray-800 border-gray-700 rounded-lg pl-10 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
      </div>
      <div class="flex items-center space-x-4">
        <div class="w-9 h-9 rounded-full bg-gray-700 flex items-center justify-center text-sm font-bold">DU</div>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 overflow-y-auto p-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-white">Daftar Jenis Pelatihan</h2>
        <button onclick="openModal()" class="px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-500 transition">
          Tambah Jenis Pelatihan
        </button>
      </div>

      <!-- TABEL -->
      <div class="bg-gray-800 rounded-xl shadow-lg overflow-hidden">
        <table class="w-full text-sm text-left" id="table-body">
          <thead class="bg-gray-800 text-xs uppercase text-gray-400">
            <tr>
              <th class="p-4"><input type="checkbox" class="rounded"></th>
              <th class="px-6 py-3">ID Jenis</th>
              <th class="px-6 py-3">Nama Jenis Pelatihan</th>
              <th class="px-6 py-3">Last Update</th>
              <th class="px-6 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody id="data-rows">
            <!-- Data akan diisi oleh JavaScript -->
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <!-- MODAL -->
  <div id="modal" class="fixed inset-0 bg-black bg-opacity-60 z-50 hidden flex items-center justify-center">
    <div class="bg-gray-800 rounded-xl shadow-2xl w-full max-w-md p-6">
      <h3 class="text-2xl font-bold text-white mb-6" id="modal-title">Tambah Jenis Pelatihan</h3>
      <form id="form-jenis">
        <input type="hidden" id="edit-index" value="-1">
        <div class="space-y-5">
          <div>
            <label class="block text-sm font-medium mb-2">ID Jenis Pelatihan</label>
            <input type="text" id="id_jenis" required class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Nama Jenis Pelatihan</label>
            <input type="text" id="nama" required class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea id="deskripsi" rows="4" class="w-full px-4 py-3 bg-gray-900 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-8">
          <button type="button" onclick="closeModal()" class="px-6 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-600">Batal</button>
          <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-500">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- SCRIPT SEMUA LOGIKA -->
  <script>
    const modal = document.getElementById('modal');
    const form = document.getElementById('form-jenis');
    const tableBody = document.getElementById('data-rows');
    let data = JSON.parse(localStorage.getItem('jenisPelatihan')) || [];

    function renderTable() {
      tableBody.innerHTML = '';
      data.forEach((item, index) => {
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-700 hover:bg-gray-750';
        row.innerHTML = `
          <td class="p-4"><input type="checkbox" class="rounded"></td>
          <td class="px-6 py-4 font-medium text-white">${item.id}</td>
          <td class="px-6 py-4">${item.nama}</td>
          <td class="px-6 py-4">${new Date().toLocaleDateString('id-ID')}</td>
          <td class="px-6 py-4 text-right space-x-4">
            <button onclick="editRow(${index})" class="text-blue-400 hover:text-blue-300">Edit</button>
            <button onclick="deleteRow(${index})" class="text-red-500 hover:text-red-400">Delete</button>
          </td>
        `;
        tableBody.appendChild(row);
      });
    }

    function openModal() {
      modal.classList.remove('hidden');
      form.reset();
      document.getElementById('edit-index').value = '-1';
      document.getElementById('modal-title').textContent = 'Tambah Jenis Pelatihan';
    }

    function closeModal() {
      modal.classList.add('hidden');
    }

    function editRow(index) {
      const item = data[index];
      document.getElementById('id_jenis').value = item.id;
      document.getElementById('nama').value = item.nama;
      document.getElementById('deskripsi').value = item.deskripsi || '';
      document.getElementById('edit-index').value = index;
      document.getElementById('modal-title').textContent = 'Edit Jenis Pelatihan';
      openModal();
    }

    function deleteRow(index) {
      if (confirm('Yakin hapus data ini?')) {
        data.splice(index, 1);
        localStorage.setItem('jenisPelatihan', JSON.stringify(data));
        renderTable();
      }
    }

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const index = document.getElementById('edit-index').value;
      const newData = {
        id: document.getElementById('id_jenis').value,
        nama: document.getElementById('nama').value,
        deskripsi: document.getElementById('deskripsi').value
      };

      if (index === '-1') {
        data.push(newData);
      } else {
        data[index] = newData;
      }

      localStorage.setItem('jenisPelatihan', JSON.stringify(data));
      renderTable();
      closeModal();
    });

    // Tutup modal kalau klik luar
    modal.addEventListener('click', function(e) {
      if (e.target === modal) closeModal();
    });

    // Init
    renderTable();
  </script>
</body>
</html>