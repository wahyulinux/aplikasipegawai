@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Daftar Pinjaman Karyawan</h1>
    @if(Auth::user()->role === 'pegawai')
        <a href="{{ route('loans.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-md transition">Ajukan Pinjaman</a>
    @endif
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
    <table class="min-w-full">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider">
            <tr>
                @if(Auth::user()->role !== 'pegawai')
                    <th class="py-4 px-6 text-left">Pegawai</th>
                @endif
                <th class="py-4 px-6 text-left">Nominal Pinjam</th>
                <th class="py-4 px-6 text-center">Tenor</th>
                <th class="py-4 px-6 text-right">Cicilan/Bulan</th>
                <th class="py-4 px-6 text-right">Sisa Hutang</th>
                <th class="py-4 px-6 text-center">Status</th>
                @if(Auth::user()->role === 'finance')
                    <th class="py-4 px-6 text-center">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
            @forelse($loans as $loan)
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                @if(Auth::user()->role !== 'pegawai')
                    <td class="py-4 px-6">
                        <p class="font-bold text-gray-800">{{ $loan->employee->nama }}</p>
                        <p class="text-xs text-gray-500">{{ $loan->employee->nip }}</p>
                    </td>
                @endif
                <td class="py-4 px-6 font-semibold">Rp {{ number_format($loan->nominal_pinjaman, 0, ',', '.') }}</td>
                <td class="py-4 px-6 text-center">{{ $loan->tenor_bulan }} Bln</td>
                <td class="py-4 px-6 text-right text-red-600 font-bold">Rp {{ number_format($loan->nominal_cicilan, 0, ',', '.') }}</td>
                <td class="py-4 px-6 text-right font-black text-blue-700">Rp {{ number_format($loan->sisa_pinjaman, 0, ',', '.') }}</td>
                <td class="py-4 px-6 text-center">
                    @if($loan->status === 'pending')
                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Pending</span>
                    @elseif($loan->status === 'approved')
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Active</span>
                    @elseif($loan->status === 'lunas')
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Lunas</span>
                    @else
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Rejected</span>
                    @endif
                </td>
                @if(Auth::user()->role === 'finance')
                    <td class="py-4 px-6 text-center space-x-2">
                        @if($loan->status === 'pending')
                            <form action="{{ route('loans.approve', $loan->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 font-bold" onclick="return confirm('Cairkan pinjaman ini?')">Approve</button>
                            </form>
                            <form action="{{ route('loans.reject', $loan->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600 font-bold" onclick="return confirm('Tolak pinjaman ini?')">Reject</button>
                            </form>
                        @else
                            <span class="text-gray-400 italic text-xs">Selesai</span>
                        @endif
                    </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-10 text-center text-gray-400 italic">Belum ada data pinjaman.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
