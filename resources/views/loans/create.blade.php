@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-10 rounded-2xl shadow-2xl border border-gray-100">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Form Pengajuan Pinjaman</h1>
        <p class="text-gray-500 mt-2 italic text-sm">*Maksimal memiliki 3 pinjaman aktif sekaligus.</p>
    </div>
    
    <form action="{{ route('loans.store') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Nominal Pinjaman (Rp)</label>
            <input type="number" name="nominal_pinjaman" step="1" required placeholder="Contoh: 1000000"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Tenor (Bulan)</label>
            <select name="tenor_bulan" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200">
                <option value="1">1 Bulan</option>
                <option value="2">2 Bulan</option>
                <option value="3">3 Bulan</option>
                <option value="6">6 Bulan</option>
                <option value="12">12 Bulan</option>
                <option value="24">24 Bulan</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Keterangan / Keperluan</label>
            <textarea name="keterangan" rows="3" placeholder="Alasan meminjam..."
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition duration-200"></textarea>
        </div>

        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
            <p class="text-xs text-blue-800 leading-relaxed">
                <span class="font-bold">Info:</span> Nominal cicilan per bulan akan otomatis dihitung oleh sistem dengan membagi rata total pinjaman dengan tenor yang dipilih. Potongan akan dimulai pada slip gaji bulan berikutnya setelah disetujui.
            </p>
        </div>

        <div class="flex items-center justify-between pt-6">
            <a href="{{ route('loans.index') }}" class="text-gray-500 hover:text-gray-800 font-bold transition">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl hover:bg-blue-700 font-black shadow-lg transform hover:-translate-y-1 transition duration-200">
                Kirim Pengajuan
            </button>
        </div>
    </form>
</div>
@endsection
