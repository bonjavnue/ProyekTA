<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RouteHelper
{
    /**
     * Get the title for the current route
     */
    public static function getPageTitle(): string
    {
        $routeName = Route::currentRouteName();
        
        // Mapping route names to display titles
        $titles = [
            // Dashboard
            'dashboard' => 'Dashboard',
            
            // Admin routes
            'jenis-pelatihan.index' => 'Jenis Pelatihan',
            'karyawan.index' => 'Data Karyawan',
            'kehadiran.index' => 'Kehadiran Pelatihan',
            'penjadwalan.index' => 'Penjadwalan Pelatihan',
            'penjadwalan.create' => 'Tambah Penjadwalan Pelatihan',
            'penjadwalan.edit' => 'Edit Penjadwalan Pelatihan',
            'penjadwalan.show' => 'Detail Penjadwalan Pelatihan',
            'supervisor.index' => 'Kelola Supervisor',
            'bagian.index' => 'Kelola Bagian',
            'admin.index' => 'Kelola Admin',
        ];
        
        return $titles[$routeName] ?? 'Admin Panel';
    }
    
    /**
     * Get breadcrumb array with clickable links
     */
    public static function getBreadcrumbs(): array
    {
        $routeName = Route::currentRouteName();
        
        // Route to breadcrumb mapping
        $breadcrumbs = [
            'dashboard' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')]
            ],
            
            // Admin routes
            'jenis-pelatihan.index' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Jenis Pelatihan', 'url' => null]
            ],
            'karyawan.index' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Data Karyawan', 'url' => null]
            ],
            'kehadiran.index' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Kehadiran Pelatihan', 'url' => null]
            ],
            'kehadiran.show' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Kehadiran Pelatihan', 'url' => route('kehadiran.index')],
                ['label' => 'Detail', 'url' => null]
            ],
            'kehadiran.export' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Kehadiran Pelatihan', 'url' => route('kehadiran.index')],
                ['label' => 'Export', 'url' => null]
            ],
            'penjadwalan.index' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Penjadwalan Pelatihan', 'url' => null]
            ],
            'penjadwalan.create' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Penjadwalan Pelatihan', 'url' => route('penjadwalan.index')],
                ['label' => 'Tambah', 'url' => null]
            ],
            'penjadwalan.edit' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Penjadwalan Pelatihan', 'url' => route('penjadwalan.index')],
                ['label' => 'Edit', 'url' => null]
            ],
            'penjadwalan.show' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Penjadwalan Pelatihan', 'url' => route('penjadwalan.index')],
                ['label' => 'Detail', 'url' => null]
            ],
            'supervisor.index' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Kelola Supervisor', 'url' => null]
            ],
            'bagian.index' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Kelola Bagian', 'url' => null]
            ],
            'admin.index' => [
                ['label' => 'Admin', 'url' => route('dashboard')],
                ['label' => 'Kelola Admin', 'url' => null]
            ],
        ];
        
        // Return breadcrumbs for current route or default
        return $breadcrumbs[$routeName] ?? [
            ['label' => 'Admin', 'url' => route('dashboard')]
        ];
    }
    
    /**
     * Get the breadcrumb path for current route (legacy)
     */
    public static function getBreadcrumb(): string
    {
        $routeName = Route::currentRouteName();
        
        // For admin routes, format as "Admin / <Page Title>"
        if (str_starts_with($routeName, 'admin') || $routeName === 'dashboard') {
            $title = self::getPageTitle();
            
            if ($routeName === 'dashboard') {
                return "Dashboard";
            }
            
            return "Admin / " . $title;
        }
        
        return "Admin / Admin Panel";
    }
}

