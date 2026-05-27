@php
    $qrData = implode("\n", [
        'Laporan : ' . ($jenis ?? 'Laporan'),
        'Periode  : ' . \Carbon\Carbon::parse($bulan)->translatedFormat('F Y'),
        'Dibuat   : ' . Auth::user()->name,
        'Jabatan  : ' . strtoupper(Auth::user()->role),
        'Tanggal  : ' . date('d/m/Y H:i'),
    ]);
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' . urlencode($qrData);
    $docCode = strtoupper(substr(md5(($jenis ?? '') . $bulan . Auth::id() . date('Ymd')), 0, 12));
@endphp

<div class="mt-12 flex justify-end">
    <div class="text-center w-64 flex flex-col justify-end items-center">
        <p class="text-xs font-bold text-gray-700 mb-4">Dibuat Oleh,</p>
        <div class="mb-4">
            <img src="{{ $qrUrl }}"
                 alt="QR Tanda Tangan"
                 class="mx-auto border p-1 bg-white shadow-sm">
            <p class="text-[9px] text-gray-400 mt-1 font-mono tracking-widest">{{ $docCode }}</p>
        </div>
        <p class="text-sm font-bold text-gray-800 border-b border-gray-800 pb-1 uppercase w-full">
            {{ Auth::user()->name }}
        </p>
        <p class="text-[10px] text-gray-500 italic mt-1">{{ strtoupper(Auth::user()->role) }}</p>
        <p class="text-[10px] text-gray-500 italic mt-1">Tgl: {{ date('d/m/Y H:i') }}</p>
    </div>
</div>
