<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    @auth
    <nav class="bg-blue-600 p-4 text-white shadow-lg no-print" x-data="{ open: false }">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <!-- Brand & Logo -->
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-xl font-bold flex items-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">Payroll App</span>
                    </a>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden md:flex space-x-1">
                        @if(Auth::user()->role === 'superadmin')
                            <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('users.*') ? 'bg-blue-800' : '' }}">Pengguna</a>
                        @else
                            @if(Auth::user()->role !== 'pegawai')
                                <a href="{{ route('employees.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('employees.*') ? 'bg-blue-800' : '' }}">Pegawai</a>
                            @endif
                            <a href="{{ route('payrolls.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('payrolls.*') ? 'bg-blue-800' : '' }}">Gaji</a>
                            <a href="{{ route('loans.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('loans.*') ? 'bg-blue-800' : '' }}">Pinjaman</a>
                            @if(Auth::user()->role !== 'pegawai')
                                <a href="{{ route('psb.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('psb.*') ? 'bg-blue-800' : '' }}">PSB</a>
                                <a href="{{ route('itj.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('itj.*') ? 'bg-blue-800' : '' }}">ITJ</a>
                            @endif
                        @endif
                        @if(in_array(Auth::user()->role, ['hrd', 'superadmin']))
                            <a href="{{ route('settings.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('settings.*') ? 'bg-blue-800' : '' }}">Pengaturan</a>
                        @endif
                    </div>
                </div>
                
                <!-- Right Side Profile & Actions -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <div class="text-right mr-2 hidden sm:block">
                        <p class="text-xs font-bold opacity-75 uppercase leading-none">{{ Auth::user()->role }}</p>
                        <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                    </div>
                    @if(Auth::user()->role !== 'pegawai')
                        <a href="{{ route('password.change') }}" title="Ganti Password" class="bg-blue-700 hover:bg-blue-800 p-2 rounded-lg text-white transition duration-300 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-800 hover:bg-red-600 px-3 py-2 sm:px-4 sm:py-2 rounded-lg text-sm font-bold transition duration-300 flex items-center space-x-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden flex items-center">
                        <button @click="open = !open" type="button" class="text-gray-200 hover:text-white focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div x-show="open" class="md:hidden mt-4 pb-2 space-y-1" style="display: none;">
                @if(Auth::user()->role === 'superadmin')
                    <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-md hover:bg-blue-700 {{ request()->routeIs('users.*') ? 'bg-blue-800' : '' }}">Pengguna</a>
                @else
                    @if(Auth::user()->role !== 'pegawai')
                        <a href="{{ route('employees.index') }}" class="block px-3 py-2 rounded-md hover:bg-blue-700 {{ request()->routeIs('employees.*') ? 'bg-blue-800' : '' }}">Pegawai</a>
                    @endif
                    <a href="{{ route('payrolls.index') }}" class="block px-3 py-2 rounded-md hover:bg-blue-700 {{ request()->routeIs('payrolls.*') ? 'bg-blue-800' : '' }}">Gaji</a>
                    <a href="{{ route('loans.index') }}" class="block px-3 py-2 rounded-md hover:bg-blue-700 {{ request()->routeIs('loans.*') ? 'bg-blue-800' : '' }}">Pinjaman</a>
                    @if(Auth::user()->role !== 'pegawai')
                        <a href="{{ route('psb.index') }}" class="block px-3 py-2 rounded-md hover:bg-blue-700 {{ request()->routeIs('psb.*') ? 'bg-blue-800' : '' }}">PSB</a>
                        <a href="{{ route('itj.index') }}" class="block px-3 py-2 rounded-md hover:bg-blue-700 {{ request()->routeIs('itj.*') ? 'bg-blue-800' : '' }}">ITJ</a>
                    @endif
                @endif
                @if(in_array(Auth::user()->role, ['hrd', 'superadmin']))
                    <a href="{{ route('settings.index') }}" class="block px-3 py-2 rounded-md hover:bg-blue-700 {{ request()->routeIs('settings.*') ? 'bg-blue-800' : '' }}">Pengaturan</a>
                @endif
                <div class="pt-4 pb-2 border-t border-blue-700">
                    <p class="px-3 text-xs font-bold opacity-75 uppercase">{{ Auth::user()->role }}</p>
                    <p class="px-3 text-sm font-medium">{{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="container mx-auto p-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
