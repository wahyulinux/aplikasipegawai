@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Data Pegawai</h1>
    @if(Auth::user()->role === 'staff')
        <a href="{{ route('employees.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 font-bold">Tambah Pegawai</a>
    @endif
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 text-gray-600 uppercase text-sm">
                <tr>
                    <th class="py-3 px-6 text-left whitespace-nowrap">NIP</th>
                    <th class="py-3 px-6 text-left whitespace-nowrap">Nama</th>
                    <th class="py-3 px-6 text-left whitespace-nowrap">Email</th>
                    <th class="py-3 px-6 text-left whitespace-nowrap">Jabatan</th>
                    <th class="py-3 px-6 text-left whitespace-nowrap">No. Rekening</th>
                    <th class="py-3 px-6 text-center whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @foreach($employees as $employee)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left font-medium whitespace-nowrap">{{ $employee->nip }}</td>
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $employee->nama }}</td>
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $employee->email ?? '-' }}</td>
                    <td class="py-3 px-6 text-left whitespace-nowrap">{{ $employee->jabatan }}</td>
                    <td class="py-3 px-6 text-left font-mono whitespace-nowrap">{{ $employee->nomor_rekening ?? '-' }}</td>
                    <td class="py-3 px-6 text-center space-x-2 whitespace-nowrap">
                        @if(Auth::user()->role === 'staff')
                            <a href="{{ route('employees.edit', $employee->id) }}" class="text-yellow-600 hover:underline font-bold">Edit</a>
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline font-bold" onclick="return confirm('Hapus pegawai ini?')">Hapus</button>
                            </form>
                        @else
                            <span class="text-gray-400 italic">No Action</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
