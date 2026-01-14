@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-gray-500 mt-2">Selamat datang di Sistem Manajemen Pelatihan Karyawan</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Pelatihan</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
            </div>
            <svg class="w-12 h-12 text-blue-100" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Karyawan</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
            </div>
            <svg class="w-12 h-12 text-yellow-100" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Kehadiran Hari Ini</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
            </div>
            <svg class="w-12 h-12 text-green-100" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Belum Hadir</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
            </div>
            <svg class="w-12 h-12 text-red-100" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
        </div>
    </div>
</div>

<!-- Welcome Message -->
<div class="bg-gradient-to-r from-brand-blue to-blue-900 rounded-lg shadow-lg p-8 text-white">
    <h2 class="text-2xl font-bold mb-4">Selamat Datang, {{ auth()->user()->email }}!</h2>
    <p class="mb-4">
        Anda login sebagai <span class="font-semibold capitalize">{{ auth()->user()->role }}</span>.
        Gunakan menu di sidebar untuk mengelola pelatihan karyawan.
    </p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <a href="{{ route('jenis-pelatihan.index') }}" class="block bg-white/10 hover:bg-white/20 rounded-lg p-4 transition">
            <p class="font-semibold">Jenis Pelatihan</p>
            <p class="text-sm text-blue-100">Kelola jenis pelatihan</p>
        </a>
        <a href="{{ route('karyawan.index') }}" class="block bg-white/10 hover:bg-white/20 rounded-lg p-4 transition">
            <p class="font-semibold">Data Karyawan</p>
            <p class="text-sm text-blue-100">Kelola data karyawan</p>
        </a>
        <a href="{{ route('supervisor.index') }}" class="block bg-white/10 hover:bg-white/20 rounded-lg p-4 transition">
            <p class="font-semibold">Kelola Supervisor</p>
            <p class="text-sm text-blue-100">Manajemen supervisor</p>
        </a>
    </div>
</div>
@endsection
