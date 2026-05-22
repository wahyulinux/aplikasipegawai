@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-4 md:space-y-0">
    <h1 class="text-2xl font-bold">Data Penggajian</h1>
    <div class="flex flex-col sm:flex-row w-full md:w-auto space-y-2 sm:space-y-0 sm:space-x-2">
        @if(Auth::user()->role !== 'pegawai')
            <form action="{{ route('payrolls.print_all') }}" method="GET" class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
                <input type="month" name="bulan" class="rounded border-gray-300 text-sm w-full sm:w-auto" required>
                <div class="flex space-x-2 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm font-bold whitespace-nowrap">Cetak Masal</button>
                    <button type="submit" formaction="{{ route('payrolls.export_excel') }}" class="w-full sm:w-auto bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-bold whitespace-nowrap">Export Excel</button>
                </div>
            </form>
        @endif
        @if(Auth::user()->role === 'staff')
            <a href="{{ route('payrolls.create') }}" class="w-full sm:w-auto bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 text-sm font-bold text-center whitespace-nowrap">Input Gaji Baru</a>
        @endif
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
                <tr>
                    <th class="py-3 px-6 text-left whitespace-nowrap">Pegawai</th>
                    <th class="py-3 px-6 text-left whitespace-nowrap">Bulan</th>
                    <th class="py-3 px-6 text-center whitespace-nowrap">Status</th>
                    <th class="py-3 px-6 text-right whitespace-nowrap">Gaji Bersih</th>
                    <th class="py-3 px-6 text-center whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @foreach($payrolls as $payroll)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left font-medium whitespace-nowrap">{{ $payroll->employee->nama }}</td>
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $payroll->bulan }}</td>
                    <td class="py-3 px-6 text-center whitespace-nowrap">
                        @if($payroll->status === 'approved')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Approved</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Pending</span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-right font-bold text-blue-600 whitespace-nowrap">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
                    <td class="py-3 px-6 text-center space-x-2 whitespace-nowrap">
                        @if($payroll->status !== 'approved')
                            @if(Auth::user()->role === 'hrd')
                                <form action="{{ route('payrolls.approve', $payroll->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 font-bold">Approve HRD</button>
                                </form>
                            @endif
                            
                            @if(Auth::user()->role === 'staff')
                                <a href="{{ route('payrolls.edit', $payroll->id) }}" class="text-yellow-600 hover:underline font-bold">Edit</a>
                            @endif
                        @endif
                        <a href="{{ route('payrolls.show', $payroll->id) }}" class="text-blue-500 hover:underline font-bold">Detail Slip</a>
                        @if($payroll->status !== 'approved' && Auth::user()->role === 'staff')
                            <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline font-bold" onclick="return confirm('Hapus data gaji ini?')">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
