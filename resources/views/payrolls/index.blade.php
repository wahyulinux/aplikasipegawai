@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Data Penggajian</h1>
    <div class="flex space-x-2">
        @if(Auth::user()->role !== 'pegawai')
            <form action="{{ route('payrolls.print_all') }}" method="GET" class="flex items-center space-x-2">
                <input type="month" name="bulan" class="rounded border-gray-300 text-sm" required>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm font-bold">Cetak Masal</button>
                <button type="submit" formaction="{{ route('payrolls.export_excel') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-bold">Export Excel</button>
            </form>
        @endif
        @if(Auth::user()->role === 'staff')
            <a href="{{ route('payrolls.create') }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600 text-sm font-bold">Input Gaji Baru</a>
        @endif
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
            <tr>
                <th class="py-3 px-6 text-left">Pegawai</th>
                <th class="py-3 px-6 text-left">Bulan</th>
                <th class="py-3 px-6 text-center">Status</th>
                <th class="py-3 px-6 text-right">Gaji Bersih</th>
                <th class="py-3 px-6 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
            @foreach($payrolls as $payroll)
            <tr class="border-b border-gray-200 hover:bg-gray-100">
                <td class="py-3 px-6 text-left font-medium">{{ $payroll->employee->nama }}</td>
                <td class="py-3 px-6 text-left">{{ $payroll->bulan }}</td>
                <td class="py-3 px-6 text-center">
                    @if($payroll->status === 'approved')
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Approved</span>
                    @else
                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold uppercase">Pending</span>
                    @endif
                </td>
                <td class="py-3 px-6 text-right font-bold text-blue-600">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
                <td class="py-3 px-6 text-center space-x-2">
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
@endsection
