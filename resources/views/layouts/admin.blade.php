<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#05339C',  // Primary Sidebar/Buttons
                            red: '#D73535',   // Danger/Delete
                            yellow: '#FFD41D', // Warning/Edit/Highlights
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        /* Custom Scrollbar untuk sidebar biru */
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">

        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="flex-shrink-0 flex flex-col transition-all duration-300 bg-brand-blue text-white shadow-xl z-20">
            <div class="h-16 flex items-center justify-center border-b border-white/10">
                <span x-show="sidebarOpen" class="text-xl font-bold tracking-wider text-white">MY APP</span>
                <span x-show="!sidebarOpen" class="text-xl font-bold text-brand-yellow">M</span>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 scrollbar-hide">
                <ul class="space-y-1 px-3">
                    @php
                        $menuItems = [
                            [
                                'name' => 'Dashboard', 
                                'active' => false, 
                                // Ikon Rumah
                                'path' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'
                            ],
                            [
                                'name' => 'Jenis Pelatihan', 
                                'active' => true, 
                                // Ikon Topi Wisuda (Training)
                                'path' => 'M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'
                            ],
                            [
                                'name' => 'Penjadwalan', 
                                'active' => false, 
                                // Ikon Kalender
                                'path' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
                            ],
                            [
                                'name' => 'Kehadiran', 
                                'active' => false, 
                                // Ikon Clipboard Check
                                'path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'
                            ],
                            [
                                'name' => 'Data Karyawan', 
                                'active' => false, 
                                // Ikon Group Users
                                'path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
                            ],
                            [
                                'name' => 'Kelola Supervisor', 
                                'active' => false, 
                                // Ikon User Cog (Admin/Settings)
                                'path' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
                            ],
                        ];
                    @endphp

                    @foreach($menuItems as $item)
                    <li>
                        <a href="#" class="flex items-center px-3 py-3 rounded-lg transition-all group 
                            {{ $item['active'] 
                                ? 'bg-white/10 text-white font-semibold border-l-4 border-brand-yellow shadow-lg' 
                                : 'text-blue-100 hover:bg-blue-800 hover:text-white' 
                            }}">
                            
                            <svg class="w-6 h-6 flex-shrink-0 {{ $item['active'] ? 'text-brand-yellow' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['path'] }}"></path>
                            </svg>
                            
                            <span x-show="sidebarOpen" class="ml-3 truncate">
                                {{ $item['name'] }}
                            </span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </nav>

            <div class="p-4 border-t border-white/10 bg-blue-900/20">
                <button @click="sidebarOpen = !sidebarOpen" class="w-full flex justify-center items-center text-blue-200 hover:text-brand-yellow transition-colors">
                    <svg class="w-6 h-6 transform transition-transform" :class="sidebarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                </button>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            <header class="flex items-center justify-between h-16 bg-white px-6 border-b border-gray-200 shadow-sm z-10">
                <div class="text-gray-500 text-sm font-medium">Admin / <span class="text-brand-blue">Jenis Pelatihan</span></div>

                <div class="flex items-center space-x-4" x-data="{ open: false }">
                    <div class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none group">
                            <div class="w-8 h-8 rounded-full bg-brand-blue text-white flex items-center justify-center font-bold text-xs border-2 border-brand-yellow">
                                AU
                            </div>
                            <span class="text-sm font-medium text-gray-700 group-hover:text-brand-blue">Admin User</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white border border-gray-100 rounded-md shadow-xl py-1 z-50 animate-fade-in-down">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-blue">Profile</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="#" class="block px-4 py-2 text-sm text-brand-red hover:bg-red-50">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>

            <footer class="bg-white border-t border-gray-200 p-4 text-center">
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} - <span class="font-bold text-brand-blue">Nama Karyawan</span>
                </p>
            </footer>

        </div>
    </div>
</body>
</html>