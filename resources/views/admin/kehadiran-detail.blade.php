@extends('layouts.admin')

@section('content')
<div class="container mx-auto" x-data="kehadiranDetailApp()">
    
    <!-- Header dengan Back Button -->
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('kehadiran.index') }}" class="p-2 bg-white rounded-lg border border-gray-200 hover:bg-gray-50 transition-all">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $jadwal->JenisPelatihan->nama_jenis }}</h1>
            <p class="text-sm text-gray-500">
                {{ $jadwal->tanggal_pelaksanaan->format('d M Y') }} | 
                {{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }} |
                {{ $jadwal->tempat }}
            </p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-6 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <input type="text" 
               x-model="searchKaryawan" 
               placeholder="Cari nama atau NIK karyawan..." 
               class="px-4 py-2 border border-gray-200 rounded-lg text-sm w-full md:flex-1 outline-none focus:ring-1 focus:ring-brand-blue">
        <div class="text-sm text-gray-500 font-medium">
            Total: <span x-text="karyawans.length" class="font-bold text-gray-800"></span> karyawan
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-6 flex flex-col sm:flex-row gap-3">
        <button @click="selectAllPresent()" 
                class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium text-sm transition-all shadow-sm">
            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Hadir Semua
        </button>
        <button @click="resetAllStatus()" 
                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium text-sm transition-all shadow-sm">
            <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Reset Semua
        </button>
    </div>

    <!-- Tabel Kehadiran -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                    <th class="px-6 py-4">Karyawan</th>
                    <th class="px-6 py-4 text-center">Tanggal</th>
                    <th class="px-6 py-4 text-center">Waktu Presensi</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" x-show="filteredKaryawans.length > 0">
                <template x-for="karyawan in filteredKaryawans" :key="karyawan.id_karyawan">
                    <tr class="hover:bg-gray-50/50 transition-colors" :class="karyawan.status === 'Belum Presensi' ? 'bg-red-50/20' : ''">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-brand-blue text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                    <span x-text="karyawan.nama_karyawan.substring(0, 2).toUpperCase()"></span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800" x-text="karyawan.nama_karyawan"></p>
                                    <p class="text-[10px] text-gray-400 uppercase" x-text="'NIK: ' + karyawan.nik"></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-mono">
                            <span x-show="karyawan.waktu_presensi" x-text="formatDate(karyawan.waktu_presensi)"></span>
                            <span x-show="!karyawan.waktu_presensi" class="text-gray-400 italic">--/--/--</span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-mono">
                            <span x-show="karyawan.waktu_presensi" x-text="formatTime(karyawan.waktu_presensi)"></span>
                            <span x-show="!karyawan.waktu_presensi" class="text-gray-400 italic">--:--:--</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <select @change="updateKaryawanStatus(karyawan.id_karyawan, $event.target.value)"
                                    :value="karyawan.status"
                                    class="text-[10px] font-bold uppercase bg-white border border-gray-200 rounded px-2 py-1 outline-none focus:ring-1 focus:ring-brand-blue transition-all"
                                    :class="statusClass(karyawan.status)">
                                <option value="Hadir">Hadir</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Izin">Izin</option>
                                <option value="Alpa">Alpa</option>
                                <option value="Belum Presensi">Belum Presensi</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            <span x-show="karyawan.dicatat_oleh === 'admin'" class="px-2 py-1 bg-blue-100 text-blue-700 text-[10px] font-bold rounded uppercase">Dicatat oleh Admin</span>
                            <span x-show="karyawan.dicatat_oleh === 'supervisor'" class="px-2 py-1 bg-orange-100 text-orange-700 text-[10px] font-bold rounded uppercase">Dicatat oleh Supervisor</span>
                            <span x-show="karyawan.dicatat_oleh === 'karyawan'" class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded uppercase">Dicatat oleh Karyawan</span>
                            <span x-show="!karyawan.dicatat_oleh" class="text-gray-400 italic text-xs">--</span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div x-show="filteredKaryawans.length === 0 && karyawans.length > 0" class="text-center py-12">
            <p class="text-gray-500">Tidak ada karyawan yang sesuai dengan pencarian</p>
        </div>
    </div>
</div>

<script>
function kehadiranDetailApp() {
    return {
        karyawans: @json($karyawans),
        searchKaryawan: '',
        jadwalId: {{ $jadwal->id_jadwal }},
        
        get filteredKaryawans() {
            return this.karyawans.filter(k => 
                k.nama_karyawan.toLowerCase().includes(this.searchKaryawan.toLowerCase()) ||
                k.nik.includes(this.searchKaryawan)
            );
        },
        
        async updateKaryawanStatus(idKaryawan, newStatus) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    alert('CSRF token tidak ditemukan');
                    return;
                }

                const response = await fetch(`/admin/kehadiran/${this.jadwalId}/${idKaryawan}`, {
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
                    alert('Error ' + response.status + ': ' + (data?.message || 'Gagal mengupdate status'));
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
                    alert('Response tidak valid dari server');
                }
            } catch (err) {
                console.error('Fetch error:', err);
                alert('Gagal mengupdate status kehadiran: ' + err.message);
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
            if (!timestamp) return '--:--:--';
            const date = new Date(timestamp);
            return date.toLocaleTimeString('id-ID');
        },
        
        formatDate(timestamp) {
            if (!timestamp) return '--/--/--';
            const date = new Date(timestamp);
            return date.toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' }).split('/').reverse().join('-');
        },
        
        async selectAllPresent() {
            if (!confirm('Ubah status semua karyawan menjadi Hadir?')) return;
            
            for (const karyawan of this.karyawans) {
                await this.updateKaryawanStatus(karyawan.id_karyawan, 'Hadir');
            }
            alert('Semua karyawan berhasil diubah menjadi Hadir');
        },
        
        async resetAllStatus() {
            if (!confirm('Reset status semua karyawan menjadi Belum Presensi?')) return;
            
            for (const karyawan of this.karyawans) {
                await this.updateKaryawanStatus(karyawan.id_karyawan, 'Belum Presensi');
            }
            alert('Semua status berhasil direset menjadi Belum Presensi');
        }
    }
}
</script>
@endsection
