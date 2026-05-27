@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
    <h1 class="text-2xl font-bold text-gray-800">Data Piket Pegawai</h1>
    <div class="flex flex-col sm:flex-row w-full md:w-auto space-y-2 sm:space-y-0 sm:space-x-2">
        <form action="{{ route('picket.report') }}" method="GET" class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
            <input type="month" name="bulan" class="rounded border-gray-300 text-sm w-full sm:w-auto" required>
            <div class="flex space-x-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm font-bold whitespace-nowrap">Cetak Rekap</button>
                <button type="submit" formaction="{{ route('picket.report_detail') }}" class="w-full sm:w-auto bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700 text-sm font-bold whitespace-nowrap">Cetak Detail</button>
            </div>
        </form>
        @if(Auth::user()->role === 'staff')
            <a href="{{ route('picket.create') }}" class="w-full md:w-auto bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-md transition text-center">Input Piket</a>
        @endif
    </div>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="py-4 px-6 text-left whitespace-nowrap">Tanggal</th>
                    <th class="py-4 px-6 text-left whitespace-nowrap">Daftar Pegawai</th>
                    <th class="py-4 px-6 text-right whitespace-nowrap">Nominal Per Orang</th>
                    <th class="py-4 px-6 text-right whitespace-nowrap">Total Biaya</th>
                    <th class="py-4 px-6 text-left whitespace-nowrap">Keterangan</th>
                    @if(Auth::user()->role === 'staff')
                        <th class="py-4 px-6 text-center whitespace-nowrap">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @forelse($pickets as $picket)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                    <td class="py-4 px-6 font-bold text-blue-600 whitespace-nowrap">{{ $picket->tanggal->format('d/m/Y') }}</td>
                    <td class="py-4 px-6">
                        <div class="flex flex-wrap gap-1">
                            @foreach($picket->employees as $emp)
                                <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-[10px] font-bold border border-indigo-100">
                                    {{ $emp->nama }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-4 px-6 text-right font-black text-green-600 whitespace-nowrap">Rp {{ number_format($picket->nominal_per_orang, 0, ',', '.') }}</td>
                    <td class="py-4 px-6 text-right font-semibold text-gray-700 whitespace-nowrap">Rp {{ number_format($picket->nominal_per_orang * $picket->employees->count(), 0, ',', '.') }}</td>
                    <td class="py-4 px-6 text-xs italic">{{ $picket->keterangan ?: '-' }}</td>
                    @if(Auth::user()->role === 'staff')
                        <td class="py-4 px-6 text-center whitespace-nowrap">
                            <form action="{{ route('picket.destroy', $picket->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline font-bold" onclick="return confirm('Hapus data piket ini?')">Hapus</button>
                            </form>
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-400 italic">Belum ada data Piket.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($pickets->hasPages())
        <div class="mt-4">{{ $pickets->links() }}</div>
    @endif
</div>
@endsection
