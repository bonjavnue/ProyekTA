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
<body class="bg-gray-50 min-h-screen" x-data="presensiApp()" @init="init()">
    <!-- Mobile: Full screen, Desktop: Centered container -->
    <div class="min-h-screen flex items-center justify-center p-2 sm:p-4 lg:p-6">
        <div class="w-full max-w-sm sm:max-w-md lg:max-w-none">
            <!-- Header -->
            <div class="bg-gradient-to-r from-brand-blue to-blue-800 rounded-t-2xl p-4 sm:p-6 text-white text-center shadow-lg">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 mx-auto mb-2 sm:mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h1 class="text-xl sm:text-2xl font-bold">Presensi Pelatihan</h1>
                <p class="text-blue-100 text-xs sm:text-sm mt-1" x-show="!loading && jadwal" x-text="jadwal?.nama_pelatihan || ''"></p>
            </div>

            <!-- Konten Utama -->
            <div class="bg-white rounded-b-2xl shadow-lg p-4 sm:p-6">
                <!-- Loading State -->
                <div x-show="loading" class="text-center py-8 sm:py-12">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 mb-4">
                        <svg class="animate-spin h-6 w-6 text-brand-blue" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 text-sm sm:text-base">Memuat data jadwal...</p>
                </div>

                <!-- Error State -->
                <div x-show="error && !loading" class="text-center py-8 sm:py-12">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <p class="text-red-600 font-medium text-sm sm:text-base" x-text="error"></p>
                </div>

                <!-- Success State (Sudah Absen) -->
                <div x-show="presentedAt && !loading && !error" class="text-center py-8 sm:py-12">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-green-600 font-bold text-base sm:text-lg mb-2">Presensi Berhasil!</p>
                    <p class="text-gray-700 text-xs sm:text-sm mb-1" x-text="`Nama: ${namaKaryawan}`"></p>
                    <p class="text-gray-700 text-xs sm:text-sm" x-text="`Waktu: ${presentedAt}`"></p>
                </div>

                <!-- Form Presensi -->
                <div x-show="!presentedAt && !loading && !error && jadwal">
                    <!-- Info Jadwal -->
                    <div class="bg-blue-50 rounded-lg p-3 sm:p-4 mb-4 sm:mb-6 border border-blue-200">
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 text-xs sm:text-sm">
                            <div>
                                <p class="text-gray-600 font-medium">Tanggal</p>
                                <p class="font-semibold text-gray-900 mt-0.5" x-text="jadwal?.tanggal || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium">Waktu</p>
                                <p class="font-semibold text-gray-900 mt-0.5 text-xs sm:text-sm" x-text="`${jadwal?.jam_mulai || '-'} - ${jadwal?.jam_selesai || '-'}`"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium">Tempat</p>
                                <p class="font-semibold text-gray-900 mt-0.5 line-clamp-2" x-text="jadwal?.tempat || '-'"></p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-medium">Sisa Waktu</p>
                                <p class="font-semibold text-brand-blue text-lg mt-0.5" x-text="timerDisplay"></p>
                            </div>
                        </div>

                        <!-- Lokasi Info (Jika ada) -->
                        <div x-show="jadwal?.location_name" class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-blue-300">
                            <p class="text-xs font-semibold text-gray-600 mb-2">📍 Area Presensi:</p>
                            <div class="bg-white rounded p-2 sm:p-3 space-y-1 text-xs sm:text-sm">
                                <p class="text-gray-700 font-semibold" x-text="jadwal?.location_name || ''"></p>
                                <p class="text-gray-600">Radius: <strong x-text="`${jadwal?.location_radius || 100}m`"></strong></p>
                                <p class="text-gray-600" x-show="userDistance !== null">Jarak: <strong x-text="`${userDistance}m`" :class="userDistance > (jadwal?.location_radius || 100) ? 'text-red-600' : 'text-green-600'"></strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Input Form -->
                    <form @submit.prevent="submitAbsen" class="space-y-3 sm:space-y-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">ID Karyawan <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="idKaryawan"
                                placeholder="Masukkan ID Karyawan"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">NIK <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="nikKaryawan"
                                placeholder="Masukkan NIK"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition text-sm"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-xs sm:text-sm font-semibold text-gray-700 mb-1.5">Nama Karyawan <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                x-model="namaKaryawan"
                                placeholder="Masukkan Nama"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-blue focus:border-transparent outline-none transition text-sm"
                                required
                            >
                        </div>

                        <button 
                            type="submit"
                            :disabled="!idKaryawan || !nikKaryawan || !namaKaryawan || isAbsen"
                            class="w-full py-2.5 sm:py-3 bg-brand-blue hover:bg-blue-900 disabled:bg-gray-400 text-white font-semibold rounded-lg transition shadow-md text-sm sm:text-base"
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
            <div class="text-center text-xs sm:text-sm text-gray-500 mt-4 sm:mt-6">
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
                timerDisplay: '00:00',
                countdownInterval: null,
                waktuBerakhirPresensi: null,
                
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

                startCountdown() {
                    // Parse waktu berakhir dari API response
                    const endTimeStr = this.waktuBerakhirPresensi;
                    
                    if (!endTimeStr) {
                        this.timerDisplay = '00:00';
                        return;
                    }

                    const endTime = new Date(endTimeStr).getTime();
                    
                    // Langsung update pertama kali
                    this.updateCountdown(endTime);
                    
                    // Update setiap 1 detik
                    if (this.countdownInterval) {
                        clearInterval(this.countdownInterval);
                    }
                    
                    this.countdownInterval = setInterval(() => {
                        this.updateCountdown(endTime);
                    }, 1000);
                },

                updateCountdown(endTime) {
                    const now = new Date().getTime();
                    const remaining = endTime - now;

                    if (remaining <= 0) {
                        this.timerDisplay = '00:00';
                        clearInterval(this.countdownInterval);
                        this.error = 'Presensi sudah berakhir';
                    } else {
                        const minutes = Math.floor(remaining / 60000);
                        const seconds = Math.floor((remaining % 60000) / 1000);
                        const formattedMins = String(minutes).padStart(2, '0');
                        const formattedSecs = String(seconds).padStart(2, '0');
                        this.timerDisplay = `${formattedMins}:${formattedSecs}`;
                    }
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
                            this.waktuBerakhirPresensi = data.jadwal.waktu_berakhir_presensi;
                            this.timerDisplay = data.jadwal.sisa_waktu;
                            this.calculateDistance(); // Hitung ulang jarak setelah jadwal loaded
                            this.$nextTick(() => {
                                this.startCountdown();
                            });
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
                            // Hentikan countdown saat berhasil absen
                            if (this.countdownInterval) {
                                clearInterval(this.countdownInterval);
                            }
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
