@extends('layouts.app')

@section('content')
<div class="no-print mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Detail Work Order PSB</h1>
        <p class="text-gray-500">Periode: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>
    </div>
    <div class="space-x-4">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-md transition duration-300">Cetak Laporan</button>
        <a href="{{ route('psb.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 font-bold shadow-md transition duration-300">Kembali</a>
    </div>
</div>

<div class="bg-white p-10 rounded-xl shadow-xl border border-gray-200" id="report-container">
    <div class="text-center mb-8 border-b-2 border-blue-600 pb-4">
        <h1 class="text-3xl font-extrabold text-blue-800 uppercase">Log Aktivitas Detail Work Order PSB</h1>
        <p class="text-gray-600 mt-1 font-semibold text-lg">Periode: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 py-3 px-4 text-center uppercase text-xs font-bold w-12">No</th>
                    <th class="border border-gray-300 py-3 px-4 text-left uppercase text-xs font-bold w-32">Kode WO</th>
                    <th class="border border-gray-300 py-3 px-4 text-center uppercase text-xs font-bold w-32">Tanggal</th>
                    <th class="border border-gray-300 py-3 px-4 text-left uppercase text-xs font-bold">Tim Pelaksana</th>
                    <th class="border border-gray-300 py-3 px-4 text-right uppercase text-xs font-bold w-36">Total Nominal</th>
                    <th class="border border-gray-300 py-3 px-4 text-left uppercase text-xs font-bold">Keterangan</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
                @forelse($workOrders as $index => $wo)
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 py-3 px-4 text-center">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 py-3 px-4 font-bold text-blue-700">{{ $wo->kode_wo }}</td>
                    <td class="border border-gray-300 py-3 px-4 text-center">{{ $wo->tanggal_pengerjaan->format('d/m/Y') }}</td>
                    <td class="border border-gray-300 py-3 px-4">
                        <ul class="list-disc list-inside text-xs font-semibold">
                            @foreach($wo->employees as $emp)
                                <li>{{ $emp->nama }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="border border-gray-300 py-3 px-4 text-right font-bold text-green-700">Rp {{ number_format($wo->nominal_total, 0, ',', '.') }}</td>
                    <td class="border border-gray-300 py-3 px-4 italic text-xs">{{ $wo->keterangan ?: '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-400 italic">Tidak ada data Work Order pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if($workOrders->count() > 0)
            <tfoot class="bg-gray-50">
                <tr>
                    <th colspan="4" class="border border-gray-300 py-3 px-4 text-right uppercase text-xs font-bold">Total Nilai Pekerjaan</th>
                    <th class="border border-gray-300 py-3 px-4 text-right font-black text-blue-800 text-sm">Rp {{ number_format($workOrders->sum('nominal_total'), 0, ',', '.') }}</th>
                    <th class="border border-gray-300 py-3 px-4"></th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @include('partials.signature_staff', ['jenis' => 'Detail Work Order PSB'])
</div>

<style>
    @media print {
        @page { size: portrait; margin: 10mm; }
        body { background: white; }
        .no-print { display: none !important; }
        nav { display: none !important; }
        main { padding: 0; margin: 0; max-width: none; width: 100%; }
        #report-container { box-shadow: none; border: none; border-radius: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd !important; }
    }
</style>
@endsection
