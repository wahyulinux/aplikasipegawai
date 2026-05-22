@extends('layouts.app')

@section('content')
<div class="no-print mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold">Cetak Masal Slip Gaji</h1>
        @if($bulan)
            <p class="text-gray-600">Periode: {{ $bulan }}</p>
        @else
            <p class="text-gray-600">Semua Periode</p>
        @endif
    </div>
    <div class="space-x-4">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 font-bold shadow-md transition duration-300">Cetak Semua</button>
        <a href="{{ route('payrolls.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 font-bold shadow-md transition duration-300">Kembali</a>
    </div>
</div>

<div class="space-y-8">
    @forelse($payrolls as $payroll)
        <div class="max-w-2xl mx-auto bg-white p-10 rounded-lg shadow-xl border border-gray-200 slip-container mb-10 relative">
            @if($payroll->status !== \App\Models\Payroll::STATUS_APPROVED)
                <div class="absolute top-0 right-0 bg-yellow-100 text-yellow-800 px-4 py-1 rounded-bl-lg font-bold uppercase text-xs no-print">
                    Belum Disetujui
                </div>
            @endif

            <div class="text-center mb-8 border-b-2 border-blue-600 pb-4">
                <h1 class="text-3xl font-extrabold text-blue-800 uppercase">Slip Gaji Pegawai</h1>
                <p class="text-gray-600 mt-1 font-semibold">Periode: {{ $payroll->bulan }}</p>
            </div>


            <div class="grid grid-cols-2 gap-4 mb-8 text-sm">
                <div>
                    <p class="text-gray-500 uppercase tracking-wider font-bold text-xs">Nama Pegawai</p>
                    <p class="text-lg font-bold text-gray-800">{{ $payroll->employee->nama }}</p>
                    @if($payroll->employee->nomor_rekening)
                        <p class="text-xs text-gray-500 mt-1">Rek: <span class="font-mono text-gray-700">{{ $payroll->employee->nomor_rekening }}</span></p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-gray-500 uppercase tracking-wider font-bold text-xs">NIP</p>
                    <p class="text-lg font-bold text-gray-800">{{ $payroll->employee->nip }}</p>
                </div>
                <div>
                    <p class="text-gray-500 uppercase tracking-wider font-bold text-xs">Jabatan</p>
                    <p class="font-semibold text-gray-700">{{ $payroll->employee->jabatan }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-10">
                <!-- Rincian Penghasilan -->
                <div>
                    <h3 class="text-sm font-bold text-green-700 uppercase border-b-2 border-green-200 mb-3 pb-1">Penghasilan (+)</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex justify-between"><span>Gaji Pokok</span> <span>{{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Tunj. Jabatan</span> <span>{{ number_format($payroll->tunjangan_jabatan, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Uang Makan</span> <span>{{ number_format($payroll->uang_makan, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Uang Kinerja</span> <span>{{ number_format($payroll->uang_kinerja, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Uang PSB</span> <span>{{ number_format($payroll->uang_psb, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Uang Kerajinan</span> <span>{{ number_format($payroll->uang_kerajinan, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Extra Fooding</span> <span>{{ number_format($payroll->uang_extra_fooding, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Insentif Jalur</span> <span>{{ number_format($payroll->insentif_narik_jalur, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Uang Lembur</span> <span>{{ number_format($payroll->uang_lembur, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Uang Piket</span> <span>{{ number_format($payroll->uang_piket, 0, ',', '.') }}</span></li>
                    </ul>
                </div>

                <!-- Rincian Potongan -->
                <div>
                    <h3 class="text-sm font-bold text-red-700 uppercase border-b-2 border-red-200 mb-3 pb-1">Potongan (-)</h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex justify-between"><span>Pot. Kinerja</span> <span>{{ number_format($payroll->potongan_kinerja, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>BPJS Ket.</span> <span>{{ number_format($payroll->bpjs_ketenagakerjaan, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>BPJS Kes.</span> <span>{{ number_format($payroll->bpjs_kesehatan, 0, ',', '.') }}</span></li>
                        <li class="flex justify-between"><span>Pot. Pinjaman</span> <span>{{ number_format($payroll->potongan_pinjaman, 0, ',', '.') }}</span></li>
                    </ul>
                </div>
            </div>

            <!-- Totalan -->
            <div class="mt-8 pt-6 border-t-2 border-gray-100 space-y-3">
                <div class="flex justify-between text-gray-700 font-bold">
                    <span>Total Penghasilan Bruto</span>
                    <span class="text-green-600">Rp {{ number_format($payroll->total_penghasilan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-700 font-bold">
                    <span>Total Potongan</span>
                    <span class="text-red-600">Rp {{ number_format($payroll->total_potongan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xl font-black bg-blue-50 p-4 rounded-lg mt-4 border-2 border-blue-100">
                    <span class="text-blue-800 uppercase">Gaji Bersih (Take Home Pay)</span>
                    <span class="text-blue-900">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Digital Signature & Approval -->
            <div class="mt-12 flex justify-end">
                <div class="text-center w-64 flex flex-col justify-end items-center">
                    <p class="text-xs font-bold text-gray-700 mb-4">Disetujui Oleh,</p>
                    @if($payroll->status === \App\Models\Payroll::STATUS_APPROVED)
                        <div class="mb-4">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(route('payrolls.show', $payroll->id) . '?verify=' . $payroll->verification_code) }}" alt="QR Code Signature" class="mx-auto border p-1 bg-white shadow-sm">
                            <p class="text-[9px] text-gray-400 mt-1 font-mono tracking-widest">{{ $payroll->verification_code }}</p>
                        </div>
                        <p class="text-sm font-bold text-gray-800 border-b border-gray-800 pb-1 uppercase w-full">
                            {{ $payroll->approver ? $payroll->approver->name : 'HRD Manager' }}
                        </p>
                        <p class="text-[10px] text-gray-500 italic mt-1">HRD Manager</p>
                        <p class="text-[10px] text-gray-500 italic mt-1">Tgl: {{ $payroll->approved_at->format('d/m/Y H:i') }}</p>
                    @else
                        <div class="w-[100px] h-[100px] border-2 border-dashed border-gray-200 flex items-center justify-center mx-auto mb-4">
                            <span class="text-[10px] text-gray-300 italic">BELUM<br>DISETUJUI</span>
                        </div>
                        <p class="text-sm font-bold text-gray-300 border-b border-gray-300 pb-1 uppercase w-full">HRD MANAGER</p>
                    @endif
                </div>
            </div>

            <div class="mt-10 text-center text-xs text-gray-400 italic border-t pt-4">
                Slip gaji ini digenerate secara otomatis oleh Sistem Penggajian Pegawai.
            </div>
        </div>
    @empty
        <div class="text-center py-20 bg-white rounded-lg border-2 border-dashed border-gray-300">
            <p class="text-gray-500 text-lg">Tidak ada data payroll untuk dicetak.</p>
        </div>
    @endforelse
</div>

<style>
    @media print {
        body { background: white; padding: 0; margin: 0; }
        .no-print { display: none; }
        nav { display: none; }
        main { padding: 0; margin: 0; width: 100%; max-width: none; }
        .slip-container { 
            box-shadow: none !important; 
            border: 1px solid #eee !important; 
            margin-bottom: 0 !important;
            page-break-after: always;
            width: 100%;
            max-width: none;
            border-radius: 0;
        }
        /* Ensure each slip starts on a new page */
        .slip-container:last-child {
            page-break-after: auto;
        }
    }
</style>
@endsection
