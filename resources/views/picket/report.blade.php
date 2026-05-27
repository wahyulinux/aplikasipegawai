@extends('layouts.app')

@section('content')
<div class="no-print mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Rekapitulasi Piket</h1>
        <p class="text-gray-500">Periode: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>
    </div>
    <div class="space-x-4">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-md transition duration-300">Cetak Laporan</button>
        <a href="{{ route('picket.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 font-bold shadow-md transition duration-300">Kembali</a>
    </div>
</div>

<div class="bg-white p-10 rounded-xl shadow-xl border border-gray-200" id="report-container">
    <div class="text-center mb-8 border-b-2 border-indigo-600 pb-4">
        <h1 class="text-3xl font-extrabold text-indigo-800 uppercase">Rekapitulasi Pendapatan Piket (Standby)</h1>
        <p class="text-gray-600 mt-1 font-semibold text-lg">Periode: {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 py-3 px-4 text-center uppercase text-xs font-bold w-16">No</th>
                    <th class="border border-gray-300 py-3 px-4 text-left uppercase text-xs font-bold">NIP</th>
                    <th class="border border-gray-300 py-3 px-4 text-left uppercase text-xs font-bold">Nama Pegawai</th>
                    <th class="border border-gray-300 py-3 px-4 text-left uppercase text-xs font-bold">Jabatan</th>
                    <th class="border border-gray-300 py-3 px-4 text-center uppercase text-xs font-bold">Total Kehadiran</th>
                    <th class="border border-gray-300 py-3 px-4 text-right uppercase text-xs font-bold">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
                @forelse($reportData as $index => $data)
                <tr class="hover:bg-gray-50">
                    <td class="border border-gray-300 py-3 px-4 text-center">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 py-3 px-4 font-mono">{{ $data->nip }}</td>
                    <td class="border border-gray-300 py-3 px-4 font-bold">{{ $data->nama }}</td>
                    <td class="border border-gray-300 py-3 px-4">{{ $data->jabatan }}</td>
                    <td class="border border-gray-300 py-3 px-4 text-center">{{ $data->total_sesi }}x</td>
                    <td class="border border-gray-300 py-3 px-4 text-right font-bold text-green-700">Rp {{ number_format($data->total_nominal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-gray-400 italic">Tidak ada data Piket pada periode ini.</td>
                </tr>
                @endforelse
            </tbody>
            @if($reportData->count() > 0)
            <tfoot class="bg-gray-50">
                <tr>
                    <th colspan="4" class="border border-gray-300 py-3 px-4 text-right uppercase text-xs font-bold">Total Keseluruhan</th>
                    <th class="border border-gray-300 py-3 px-4 text-center font-bold">{{ $reportData->sum('total_sesi') }}x</th>
                    <th class="border border-gray-300 py-3 px-4 text-right font-black text-blue-800">Rp {{ number_format($reportData->sum('total_nominal'), 0, ',', '.') }}</th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @include('partials.signature_staff', ['jenis' => 'Rekapitulasi Piket'])
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
