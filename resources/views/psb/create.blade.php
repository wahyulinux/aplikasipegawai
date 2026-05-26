@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-10 rounded-2xl shadow-2xl border border-gray-100 mt-10">
    <div class="mb-8 border-b pb-4">
        <h1 class="text-3xl font-extrabold text-gray-900">Input Work Order PSB</h1>
        <p class="text-sm text-gray-500 mt-2">Tarif WO saat ini: <span class="font-bold text-blue-600">Rp {{ number_format($defaultNominal, 0, ',', '.') }}</span></p>
    </div>
    
    <form action="{{ route('psb.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Kode Work Order (WO)</label>
                <input type="text" placeholder="[Otomatis Dibuat Oleh Sistem]" readonly disabled
                    class="w-full px-4 py-3 border border-gray-200 bg-gray-100 text-gray-500 rounded-xl focus:outline-none transition font-bold italic">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Tanggal Pengerjaan</label>
                <input type="date" name="tanggal_pengerjaan" required value="{{ date('Y-m-d') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Pilih Pegawai Pelaksana (Maksimal 3 Orang)</label>
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto">
                @foreach($employees as $emp)
                    <label class="flex items-center space-x-3 p-2 hover:bg-white rounded-lg transition cursor-pointer border border-transparent hover:border-blue-200">
                        <input type="checkbox" name="employee_ids[]" value="{{ $emp->id }}" 
                            class="employee-checkbox w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <div class="text-sm">
                            <p class="font-bold text-gray-800">{{ $emp->nama }}</p>
                            <p class="text-xs text-gray-500">{{ $emp->jabatan }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('employee_ids')
                <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
            @enderror
            <p id="counter-msg" class="text-[10px] text-gray-500 mt-2 italic">Terpilih: 0 / 3 Orang</p>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Keterangan Tambahan</label>
            <textarea name="keterangan" rows="2" placeholder="Alamat atau detail lainnya..."
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition"></textarea>
        </div>

        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
            <p class="text-xs text-blue-800 leading-relaxed">
                <span class="font-bold">Sistem Bagi Rata:</span> Nominal WO sebesar <strong>Rp {{ number_format($defaultNominal, 0, ',', '.') }}</strong> akan otomatis dibagi rata berdasarkan jumlah pegawai yang dipilih di atas.
            </p>
        </div>

        <div class="pt-6 flex items-center justify-between">
            <a href="{{ route('psb.index') }}" class="text-gray-500 hover:text-gray-800 font-bold transition">Batal</a>
            <button type="submit" id="submit-btn" class="bg-blue-600 text-white px-8 py-3 rounded-xl hover:bg-blue-700 font-black shadow-lg transform hover:-translate-y-1 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                Simpan Work Order
            </button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.employee-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const checkedCount = document.querySelectorAll('.employee-checkbox:checked').length;
        document.getElementById('counter-msg').innerText = `Terpilih: ${checkedCount} / 3 Orang`;
        
        // Disable other checkboxes if 3 are selected
        if (checkedCount >= 3) {
            document.querySelectorAll('.employee-checkbox:not(:checked)').forEach(cb => {
                cb.disabled = true;
            });
        } else {
            document.querySelectorAll('.employee-checkbox').forEach(cb => {
                cb.disabled = false;
            });
        }

        // Disable submit button if 0 are selected
        document.getElementById('submit-btn').disabled = (checkedCount === 0);
    });
});
</script>
@endsection
