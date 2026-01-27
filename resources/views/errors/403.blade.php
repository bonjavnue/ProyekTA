<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-red-50 to-red-100">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="max-w-md w-full text-center">
            <!-- Error Icon -->
            <div class="mb-8">
                <svg class="w-24 h-24 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4v2m0 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="text-6xl font-bold text-red-600 mb-2">403</h1>
            
            <!-- Error Title -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Akses Ditolak</h2>
            
            <!-- Error Message -->
            <p class="text-gray-600 mb-8">
                Anda tidak memiliki izin untuk mengakses halaman ini. Hubungi administrator jika Anda merasa ini adalah kesalahan.
            </p>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ url('/dashboard') }}" class="inline-block w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all transform hover:-translate-y-0.5 shadow-lg">
                    Kembali ke Dashboard
                </a>
                <a href="{{ url('/') }}" class="inline-block w-full px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition-all">
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Additional Info -->
            <p class="text-sm text-gray-500 mt-8">
                Jika masalah berlanjut, silakan hubungi tim dukungan kami.
            </p>
        </div>
    </div>
</body>
</html>
