@extends('layouts.app')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-500 text-sm">Berikut adalah ringkasan finansial Anda untuk bulan {{ \Carbon\Carbon::parse($currentMonth)->translatedFormat('F Y') }}.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Card Penghasilan WO -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-green-100 font-bold tracking-wider uppercase text-xs">Total Pendapatan WO</h3>
            <svg class="w-8 h-8 text-green-200 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="text-3xl font-black mb-1">Rp {{ number_format($totalWoBulanIni, 0, ',', '.') }}</p>
        <p class="text-xs text-green-100 italic">Bulan Ini (Belum ditambahkan ke Gaji Pokok)</p>
        
        <div class="mt-4 pt-4 border-t border-green-400 border-opacity-50 flex justify-between text-sm font-medium">
            <span>PSB: Rp {{ number_format($totalPsbBulanIni, 0, ',', '.') }}</span>
            <span>ITJ: Rp {{ number_format($totalItjBulanIni, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Card Sisa Hutang -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-6 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-red-100 font-bold tracking-wider uppercase text-xs">Total Sisa Pinjaman</h3>
            <svg class="w-8 h-8 text-red-200 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <p class="text-3xl font-black mb-1">Rp {{ number_format($totalSisaHutang, 0, ',', '.') }}</p>
        <p class="text-xs text-red-100 italic">Dari {{ $activeLoans->count() }} pinjaman aktif</p>

        <div class="mt-4 pt-4 border-t border-red-400 border-opacity-50 text-sm font-medium">
            <span class="block">Estimasi Potongan Gaji Bulan Ini:</span>
            <span class="block text-lg font-bold">Rp {{ number_format($totalCicilanBulanIni, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h2 class="text-lg font-bold text-gray-800">Rincian Pinjaman Aktif</h2>
        <a href="{{ route('loans.index') }}" class="text-sm text-blue-600 hover:underline font-bold">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-white text-gray-500 uppercase text-xs font-bold tracking-wider border-b">
                <tr>
                    <th class="py-4 px-6 text-left whitespace-nowrap">Tanggal</th>
                    <th class="py-4 px-6 text-left whitespace-nowrap">Pinjaman Awal</th>
                    <th class="py-4 px-6 text-right whitespace-nowrap">Cicilan/Bulan</th>
                    <th class="py-4 px-6 text-right whitespace-nowrap">Sisa Hutang</th>
                    <th class="py-4 px-6 text-center whitespace-nowrap">Status</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @forelse($activeLoans as $loan)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="py-4 px-6 whitespace-nowrap">{{ $loan->created_at->format('d M Y') }}</td>
                    <td class="py-4 px-6 whitespace-nowrap font-medium text-gray-700">Rp {{ number_format($loan->nominal_pinjaman, 0, ',', '.') }}</td>
                    <td class="py-4 px-6 text-right whitespace-nowrap text-red-500 font-bold">Rp {{ number_format($loan->nominal_cicilan, 0, ',', '.') }}</td>
                    <td class="py-4 px-6 text-right whitespace-nowrap font-black text-blue-600">Rp {{ number_format($loan->sisa_pinjaman, 0, ',', '.') }}</td>
                    <td class="py-4 px-6 text-center whitespace-nowrap">
                        @if($loan->status === 'pending')
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Menunggu Persetujuan</span>
                        @else
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Sedang Berjalan</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-400 italic">Anda tidak memiliki pinjaman aktif saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
