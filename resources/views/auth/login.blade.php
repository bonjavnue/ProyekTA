<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-gray-900">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&q=80" 
                 class="w-full h-full object-cover opacity-40" alt="Office Background">
            <div class="absolute inset-0 bg-gradient-to-t from-brand-blue/80 to-transparent"></div>
        </div>

        <div class="relative z-10 w-full max-w-5xl flex flex-col md:flex-row items-center p-4">
            
            <div class="hidden md:block md:w-1/2 text-white pr-12">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-brand-yellow/20 border border-brand-yellow/30 text-brand-yellow text-xs font-bold mb-6 tracking-widest uppercase">
                    Admin Portal v2.0
                </div>
                <h1 class="text-5xl font-extrabold leading-tight mb-6">
                    Mulai Kelola <br> <span class="text-brand-yellow">Data Karyawan</span> <br> Lebih Cepat.
                </h1>
                <p class="text-blue-100 text-lg opacity-80 leading-relaxed">
                    Sistem manajemen terintegrasi untuk efisiensi operasional dan penjadwalan pelatihan yang lebih terstruktur.
                </p>
                <div class="mt-10 flex items-center space-x-4">
                    <div class="flex -space-x-2">
                        <img class="w-10 h-10 rounded-full border-2 border-brand-blue" src="https://i.pravatar.cc/100?u=1" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-brand-blue" src="https://i.pravatar.cc/100?u=2" alt="">
                        <img class="w-10 h-10 rounded-full border-2 border-brand-blue" src="https://i.pravatar.cc/100?u=3" alt="">
                    </div>
                    <p class="text-sm text-blue-200">Dipercaya oleh +500 Supervisor</p>
                </div>
            </div>

            <div class="w-full md:w-[450px]">
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-8 md:p-10 rounded-[2rem] shadow-2xl">
                    <div class="text-center md:text-left mb-8">
                        <h2 class="text-2xl font-bold text-white">Selamat Datang</h2>
                        <p class="text-blue-200/70 text-sm mt-1">Silakan masuk untuk melanjutkan akses.</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:ring-2 focus:ring-brand-yellow focus:bg-white/20 outline-none transition-all text-white placeholder-blue-200/50"
                                placeholder="Email Address">
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-brand-yellow text-xs" />
                        </div>

                        <div>
                            <input id="password" type="password" name="password" required 
                                class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:ring-2 focus:ring-brand-yellow focus:bg-white/20 outline-none transition-all text-white placeholder-blue-200/50"
                                placeholder="Password">
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-brand-yellow text-xs" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-brand-yellow focus:ring-brand-yellow w-4 h-4">
                                <span class="ml-2 text-xs text-blue-200">Ingat Saya</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-brand-yellow hover:underline">Lupa Password?</a>
                            @endif
                        </div>

                        <button type="submit" 
                            class="w-full bg-brand-yellow hover:bg-yellow-400 text-brand-blue font-black py-4 rounded-2xl shadow-lg shadow-yellow-500/20 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                            <span>LOGIN KE SISTEM</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-white/10 text-center">
                        <p class="text-xs text-blue-200/50 uppercase tracking-widest leading-loose">
                            Secure Access &bull; Managed by IT Div.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>