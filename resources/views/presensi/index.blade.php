<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Presensi Pelatihan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#05339C',
                            red: '#D73535',
                            yellow: '#FFD41D',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50" x-data="presensiApp()" @init="init()">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="bg-gradient-to-r from-brand-blue to-blue-800 rounded-t-2xl p-6 text-white text-center shadow-lg">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h1 class="text-2xl font-bold">Presensi Pelatihan</h1>
                <p class="text-blue-100 text-sm mt-1" x-show="!loading && jadwal" x-text="jadwal?.nama_pelatihan || ''"></p>
            </div>

            <!-- Konten Utama -->
            <div class="bg-white rounded-b-2xl shadow-lg p-6">
                <!-- Loading State -->
                <div x-show="loading" class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mb-4">
                        <svg class="animate-spin h-6 w-6 text-brand-blue" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600">Memuat data jadwal...</p>
                </div>

                <!-- Error State -->
                <div x-show="error && !loading" class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <p class="text-red-600 font-medium" x-text="error"></p>
                </div>

                <!-- Success State (Sudah Absen) -->
                <div x-show="presentedAt && !loading && !error" class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-green-600 font-bold text-lg mb-2">Presensi Berhasil!</p>
                    <p class="text-gray-700 text-sm mb-1" x-text="`Nama: ${namaKaryawan}`"></p>
                    <p class="text-gray-700 text-sm" x-text="`Waktu: ${presentedAt}`"></p>
                </div>

                <!-- Form Presensi -->
                <div x-show="!presentedAt && !loading && !error && jadwal">
                    <!-- Info Jadwal -->
                    <div class="bg-blue-50 rounded-lg p-4 mb-6 border border-blue-200">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Tanggal</p>
                                <p class="font-semibold text-gray-900" x-text="jadwal?.tanggal || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Waktu</p>
                                <p class="font-semibold text-gray-900" x-text="`${jadwal?.jam_mulai || '-'} - ${jadwal?.jam_selesai || '-'}`"></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Tempat</p>
                                <p class="font-semibold text-gray-900" x-text="jadwal?.tempat || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-600">Sisa Waktu</p>
                                <p class="font-semibold text-brand-blue" x-text="jadwal?.sisa_waktu || '-'"></p>
                            </div>
                        </div>

                        <!-- Lokasi Info (Jika ada) -->
                        <div x-show="jadwal?.location_name" class="mt-4 pt-4 border-t border-blue-300">
                            <p class="text-xs text-gray-600 mb-2">📍 <strong>Area Presensi:</strong></p>
                            <div class="bg-white rounded p-3 space-y-1 text-sm">
                                <p class="text-gray-700"><strong x-text="jadwal?.location_name || ''"></strong></p>
                                <p class="text-gray-600">Radius: <strong x-text="`${jadwal?.location_radius || 100} meter`"></strong></p>
                                <p class="text-gray-600" x-show="userDistance !== null">Jarak Anda: <strong x-text="`${userDistance} meter`" :class="userDistance > (jadwal?.location_radius || 100) ? 'text-red-600' : 'text-green-600'"></strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Input Form -->
                    <form @submit.prevent="submitAbsen" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">ID Karyawan <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="idKaryawan"
                                placeholder="Masukkan ID Karyawan"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">NIK <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="nikKaryawan"
                                placeholder="Masukkan NIK"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Karyawan <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="namaKaryawan"
                                placeholder="Masukkan Nama"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition"
                                required
                            >
                        </div>

                        <button 
                            type="submit"
                            :disabled="!idKaryawan || !nikKaryawan || !namaKaryawan || isAbsen"
                            class="w-full py-3 bg-brand-blue hover:bg-blue-900 disabled:bg-gray-400 text-white font-bold rounded-lg transition shadow-md"
                        >
                            <span x-show="!isAbsen">
                                <span x-show="userLatitude && userLongitude">✓ Absen Sekarang</span>
                                <span x-show="!userLatitude || !userLongitude">📍 Aktifkan Lokasi Dulu</span>
                            </span>
                            <span x-show="isAbsen" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center text-sm text-gray-500 mt-6">
                <p>&copy; 2026 - Sistem Presensi Pelatihan</p>
            </div>
        </div>
    </div>

    <script>
        function presensiApp() {
            return {
                id_jadwal: '{{ $id_jadwal }}',
                token: '{{ $token }}',
                loading: true,
                jadwal: null,
                error: null,
                idKaryawan: '',
                nikKaryawan: '',
                namaKaryawan: '',
                presentedAt: null,
                isAbsen: false,
                userLatitude: null,
                userLongitude: null,
                userDistance: null,
                
                async init() {
                    // Minta izin akses geolokasi saat init
                    this.requestGeolocation();
                    await this.loadJadwal();
                },
                
                requestGeolocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.userLatitude = position.coords.latitude;
                                this.userLongitude = position.coords.longitude;
                                this.calculateDistance();
                                console.log('Lokasi berhasil didapat:', this.userLatitude, this.userLongitude);
                            },
                            (error) => {
                                console.warn('Geolocation error:', error);
                                // Izin ditolak atau error, lanjutkan tanpa lokasi
                                if (error.code === error.PERMISSION_DENIED) {
                                    this.error = 'Akses lokasi ditolak. Aktifkan izin lokasi di browser untuk fitur presensi berbasis lokasi.';
                                }
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    } else {
                        console.warn('Geolocation tidak didukung browser ini');
                    }
                },

                calculateDistance() {
                    if (!this.userLatitude || !this.userLongitude || !this.jadwal) {
                        return;
                    }

                    if (!this.jadwal.location_latitude || !this.jadwal.location_longitude) {
                        this.userDistance = null;
                        return;
                    }

                    // Haversine formula
                    const R = 6371000; // Radius bumi dalam meter
                    const toRad = (deg) => (deg * Math.PI) / 180;
                    
                    const dLat = toRad(this.jadwal.location_latitude - this.userLatitude);
                    const dLon = toRad(this.jadwal.location_longitude - this.userLongitude);
                    
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                              Math.cos(toRad(this.userLatitude)) * Math.cos(toRad(this.jadwal.location_latitude)) *
                              Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    this.userDistance = Math.round(R * c);
                },
                
                async loadJadwal() {
                    this.loading = true;
                    this.error = null;
                    
                    try {
                        const response = await fetch(`/api/jadwal/${this.id_jadwal}`);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            this.jadwal = data.jadwal;
                            this.calculateDistance(); // Hitung ulang jarak setelah jadwal loaded
                        } else {
                            this.error = data.message || 'Gagal memuat data jadwal';
                        }
                    } catch (err) {
                        console.error('Error loading jadwal:', err);
                        this.error = 'Gagal memuat data jadwal: ' + err.message;
                    } finally {
                        this.loading = false;
                    }
                },
                
                async submitAbsen() {
                    if (!this.idKaryawan || !this.nikKaryawan || !this.namaKaryawan) {
                        this.error = 'Semua field harus diisi!';
                        return;
                    }

                    // Validasi lokasi jika jadwal punya lokasi
                    if (this.jadwal.location_latitude && this.jadwal.location_longitude) {
                        if (!this.userLatitude || !this.userLongitude) {
                            this.error = 'Lokasi Anda tidak dapat diakses. Pastikan Anda memberikan izin akses lokasi.';
                            return;
                        }

                        const maxDistance = this.jadwal.location_radius || 100;
                        if (this.userDistance > maxDistance) {
                            this.error = `Anda berada di luar area presensi. Jarak: ${this.userDistance}m (Max: ${maxDistance}m)`;
                            return;
                        }
                    }

                    this.isAbsen = true;
                    this.error = null;

                    try {
                        const response = await fetch(`/api/presensi`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                id_jadwal: this.id_jadwal,
                                token: this.token,
                                id_karyawan: this.idKaryawan,
                                nik: this.nikKaryawan,
                                nama: this.namaKaryawan,
                                user_latitude: this.userLatitude,
                                user_longitude: this.userLongitude
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.presentedAt = new Date().toLocaleString('id-ID');
                        } else {
                            this.error = result.message || 'Gagal melakukan presensi';
                        }
                    } catch (err) {
                        console.error('Error submitting attendance:', err);
                        this.error = 'Terjadi kesalahan saat melakukan presensi: ' + err.message;
                    } finally {
                        this.isAbsen = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
