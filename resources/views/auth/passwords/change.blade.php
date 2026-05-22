@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-10 rounded-2xl shadow-2xl border border-gray-100 mt-10">
    <div class="text-center mb-8">
        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900">Ganti Password</h1>
        <p class="text-sm text-gray-500 mt-2">Pastikan password baru Anda kuat dan aman.</p>
    </div>
    
    <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Password Saat Ini</label>
            <input type="password" name="current_password" required 
                class="w-full px-4 py-3 border @error('current_password') border-red-500 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            @error('current_password')
                <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Password Baru</label>
            <input type="password" name="password" required minlength="8"
                class="w-full px-4 py-3 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            @error('password')
                <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" required minlength="8"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
            <a href="{{ route('payrolls.index') }}" class="text-gray-500 hover:text-gray-800 font-bold transition">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 font-black shadow-lg transform hover:-translate-y-0.5 transition duration-200">
                Simpan Password
            </button>
        </div>
    </form>
</div>
@endsection
