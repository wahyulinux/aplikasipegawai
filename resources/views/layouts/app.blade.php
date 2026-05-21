<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @auth
    <nav class="bg-blue-600 p-4 text-white shadow-lg no-print">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <a href="/" class="text-xl font-bold flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Payroll App</span>
                </a>
                <div class="space-x-1">
                    @if(Auth::user()->role === 'superadmin')
                        <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('users.*') ? 'bg-blue-800' : '' }}">Pengguna</a>
                    @else
                        @if(Auth::user()->role !== 'pegawai')
                            <a href="{{ route('employees.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('employees.*') ? 'bg-blue-800' : '' }}">Pegawai</a>
                        @endif
                        <a href="{{ route('payrolls.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('payrolls.*') ? 'bg-blue-800' : '' }}">Gaji</a>
                        <a href="{{ route('loans.index') }}" class="px-3 py-2 rounded-md hover:bg-blue-700 transition {{ request()->routeIs('loans.*') ? 'bg-blue-800' : '' }}">Pinjaman</a>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="text-right mr-2 hidden sm:block">
                    <p class="text-xs font-bold opacity-75 uppercase leading-none">{{ Auth::user()->role }}</p>
                    <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-800 hover:bg-red-600 px-4 py-2 rounded-lg text-sm font-bold transition duration-300 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
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
