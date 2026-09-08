@extends('layouts.app')

@section('content')

<div class="h-screen overflow-hidden bg-[#F8FAFC] flex" x-data="{ openProfileModal: {{ $errors->any() ? 'true' : 'false' }}, mobileMenuOpen: false }">

    <!-- Mobile backdrop -->
    <div x-show="mobileMenuOpen" 
         style="display: none;" 
         class="fixed inset-0 z-[4000] bg-slate-900/50 backdrop-blur-sm lg:hidden" 
         @click="mobileMenuOpen = false" 
         x-transition.opacity></div>

    {{-- Admin Sidebar --}}
    <div :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'" 
         class="fixed inset-y-0 left-0 z-[4001] flex h-full w-[280px] flex-col border-r border-slate-200 bg-white transition-transform duration-300 lg:static lg:translate-x-0">
        
        {{-- Header --}}
        <div class="flex h-24 shrink-0 items-center justify-between border-b border-slate-200 px-6">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logo-gps.png') }}" alt="GPS Tracker" class="h-12 w-12 object-contain">
                <div>
                    <h1 class="text-sm font-bold uppercase tracking-[0.35em] text-[#2563EB]">ADMIN</h1>
                    <p class="mt-1 text-sm text-slate-500">Dashboard</p>
                </div>
            </div>
            <!-- Close Mobile Menu -->
            <button @click="mobileMenuOpen = false" type="button" class="lg:hidden rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Nav Links --}}
        <div class="flex-1 flex flex-col justify-center px-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:scale-[1.02]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Statistik
            </a>
            
            <a href="{{ route('admin.devices.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-300 {{ request()->routeIs('admin.devices.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:scale-[1.02]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
                Manajemen Perangkat
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600 hover:scale-[1.02]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Manajemen Pengguna
            </a>
        </div>
        
        {{-- Footer Sidebar --}}
        <div class="p-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} GPS Tracker<br>Admin Panel
        </div>
    </div>

    {{-- Main Content --}}
    <div class="relative z-10 flex flex-1 flex-col overflow-y-auto">
        {{-- Top Header --}}
        <header class="sticky top-0 z-20 flex h-24 shrink-0 items-center justify-between border-b border-slate-200 bg-white/90 px-4 lg:px-10 backdrop-blur-md">
            <div class="flex items-center gap-3">
                <!-- Hamburger Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="lg:hidden rounded-xl p-2.5 text-slate-500 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-xl lg:text-2xl font-bold text-slate-900 tracking-tight">@yield('page_title', 'Admin Panel')</h2>
                    <p class="text-xs lg:text-sm font-medium text-slate-500 mt-1 hidden sm:block">@yield('page_description', 'Kendali penuh atas sistem ini.')</p>
                </div>
            </div>
            
            {{-- Profile Dropdown in Header --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-3 rounded-full py-1.5 px-3 hover:bg-slate-50 transition border border-transparent hover:border-slate-200">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-500 font-medium">Administrator</p>
                    </div>
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white shadow-md">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                     class="absolute right-0 top-full mt-2 w-48 rounded-xl border border-slate-200 bg-white py-1 shadow-lg"
                     style="display: none;">
                    
                    <button @click.prevent="openProfileModal = true; open = false" class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil Admin
                    </button>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v1" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-4 lg:p-8">
            @if(session('success'))
                <div class="mb-8 rounded-2xl border border-green-200 bg-green-50 p-4 text-green-700 font-medium">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700 font-medium">
                    {{ session('error') }}
                </div>
            @endif
            
            @yield('admin_content')
        </main>
    </div>

    {{-- Admin Profile Modal --}}
    <div x-show="openProfileModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 sm:p-0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl"
             @click.outside="openProfileModal = false"
             x-show="openProfileModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-8 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-8 sm:scale-95">
             
             <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                 <h3 class="text-lg font-bold text-slate-900">Profil Admin</h3>
                 <button @click="openProfileModal = false" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                     </svg>
                 </button>
             </div>

             <div class="p-6">
                 <form action="{{ route('admin.profile.update') }}" method="POST">
                     @csrf
                     @method('PATCH')
                     
                     <div class="space-y-5">
                         <div>
                             <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                             <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                             @error('name')
                                 <span class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                             @enderror
                         </div>

                         <div>
                             <label for="email" class="block text-sm font-medium text-slate-700">Email Login</label>
                             <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                             @error('email')
                                 <span class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                             @enderror
                         </div>
                         
                         <hr class="border-slate-100">

                         <div>
                             <h4 class="text-sm font-bold text-slate-900">Ubah Password</h4>
                             <p class="text-xs text-slate-500">Kosongkan jika tidak ingin mengubah password.</p>
                         </div>
                         
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div>
                                 <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
                                 <input type="password" name="password" id="password" class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                                 @error('password')
                                     <span class="mt-1 block text-xs text-red-500">{{ $message }}</span>
                                 @enderror
                             </div>
                             <div>
                                 <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                                 <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1.5 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                             </div>
                         </div>
                     </div>
                     
                     <div class="mt-8 flex justify-end gap-3">
                         <button type="button" @click="openProfileModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                             Batal
                         </button>
                         <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/30">
                             Simpan Perubahan
                         </button>
                     </div>
                 </form>
             </div>
        </div>
    </div>

</div>

@endsection
