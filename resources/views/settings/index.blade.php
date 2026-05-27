@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-10 rounded-2xl shadow-2xl border border-gray-100 mt-10">
    <div class="text-center mb-8">
        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900">Pengaturan Aplikasi</h1>
        <p class="text-sm text-gray-500 mt-2">Kelola parameter operasional perusahaan.</p>
    </div>
    
    <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach($settings as $setting)
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">{{ $setting->description }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 font-bold">Rp</span>
                    <input type="number" name="{{ $setting->key }}" value="{{ (int)$setting->value }}" required
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
            </div>
        @endforeach

        <div class="pt-4 border-t border-gray-100 flex items-center justify-end">
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl hover:bg-blue-700 font-black shadow-lg transform hover:-translate-y-0.5 transition duration-200">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
