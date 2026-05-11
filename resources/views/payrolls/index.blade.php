@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Data Penggajian</h1>
    <a href="{{ route('payrolls.create') }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Input Gaji Baru</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
            <tr>
                <th class="py-3 px-6 text-left">Pegawai</th>
                <th class="py-3 px-6 text-left">Bulan</th>
                <th class="py-3 px-6 text-right">Penghasilan</th>
                <th class="py-3 px-6 text-right">Potongan</th>
                <th class="py-3 px-6 text-right font-bold">Gaji Bersih</th>
                <th class="py-3 px-6 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
            @foreach($payrolls as $payroll)
            <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6 text-left">{{ $payroll->employee->nama }}</td>
                <td class="py-3 px-6 text-left">{{ $payroll->bulan }}</td>
                <td class="py-3 px-6 text-right text-green-600 font-semibold">Rp {{ number_format($payroll->total_penghasilan, 0, ',', '.') }}</td>
                <td class="py-3 px-6 text-right text-red-600">Rp {{ number_format($payroll->total_potongan, 0, ',', '.') }}</td>
                <td class="py-3 px-6 text-right font-bold text-blue-600">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
                <td class="py-3 px-6 text-center space-x-2">
                    <a href="{{ route('payrolls.show', $payroll->id) }}" class="text-blue-500 hover:underline">Detail Slip</a>
                    <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Hapus data gaji ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
