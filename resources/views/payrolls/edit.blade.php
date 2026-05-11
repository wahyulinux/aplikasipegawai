@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-6">Edit Data Penggajian</h1>
    
    <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informasi Dasar -->
            <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded border">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Pegawai</label>
                    <select name="employee_id" class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ $payroll->employee_id == $employee->id ? 'selected' : '' }}>
                                {{ $employee->nama }} ({{ $employee->jabatan }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Bulan (YYYY-MM)</label>
                    <input type="month" name="bulan" value="{{ $payroll->bulan }}" class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>

            <!-- Bagian Penghasilan -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-green-600 border-b pb-2">Penghasilan</h3>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Gaji Pokok</label>
                    <input type="number" step="1" name="gaji_pokok" value="{{ (int)$payroll->gaji_pokok }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Tunjangan Jabatan</label>
                    <input type="number" step="1" name="tunjangan_jabatan" value="{{ (int)$payroll->tunjangan_jabatan }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Uang Makan</label>
                    <input type="number" step="1" name="uang_makan" value="{{ (int)$payroll->uang_makan }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Uang Kinerja</label>
                    <input type="number" step="1" name="uang_kinerja" value="{{ (int)$payroll->uang_kinerja }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Uang PSB</label>
                    <input type="number" step="1" name="uang_psb" value="{{ (int)$payroll->uang_psb }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Uang Kerajinan</label>
                    <input type="number" step="1" name="uang_kerajinan" value="{{ (int)$payroll->uang_kerajinan }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Uang Extra Fooding</label>
                    <input type="number" step="1" name="uang_extra_fooding" value="{{ (int)$payroll->uang_extra_fooding }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Insentif Narik Jalur</label>
                    <input type="number" step="1" name="insentif_narik_jalur" value="{{ (int)$payroll->insentif_narik_jalur }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Uang Lembur</label>
                    <input type="number" step="1" name="uang_lembur" value="{{ (int)$payroll->uang_lembur }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Uang Piket</label>
                    <input type="number" step="1" name="uang_piket" value="{{ (int)$payroll->uang_piket }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-green-500">
                </div>
            </div>

            <!-- Bagian Potongan -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-red-600 border-b pb-2">Potongan</h3>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Potongan Kinerja</label>
                    <input type="number" step="1" name="potongan_kinerja" value="{{ (int)$payroll->potongan_kinerja }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">BPJS Ketenagakerjaan</label>
                    <input type="number" step="1" name="bpjs_ketenagakerjaan" value="{{ (int)$payroll->bpjs_ketenagakerjaan }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>
                <div>
                    <label class="block text-gray-700 text-xs font-bold mb-1">Potongan Pinjaman</label>
                    <input type="number" step="1" name="potongan_pinjaman" value="{{ (int)$payroll->potongan_pinjaman }}" class="w-full px-3 py-1 border rounded focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end space-x-4 border-t pt-6">
            <a href="{{ route('payrolls.index') }}" class="text-gray-600 hover:underline font-bold">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-8 py-2 rounded hover:bg-blue-700 font-bold shadow-md">Perbarui Payroll</button>
        </div>
    </form>
</div>
@endsection
