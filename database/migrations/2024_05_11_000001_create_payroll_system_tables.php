<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $column) {
            $column->id();
            $column->string('nip')->unique();
            $column->string('nama');
            $column->string('jabatan');
            $column->timestamps();
        });

        Schema::create('payrolls', function (Blueprint $column) {
            $column->id();
            $column->foreignId('employee_id')->constrained()->onDelete('cascade');
            $column->string('bulan'); // Format: YYYY-MM
            
            // Penghasilan
            $column->decimal('gaji_pokok', 15, 2)->default(0);
            $column->decimal('tunjangan_jabatan', 15, 2)->default(0);
            $column->decimal('uang_makan', 15, 2)->default(0);
            $column->decimal('uang_kinerja', 15, 2)->default(0);
            $column->decimal('uang_psb', 15, 2)->default(0);
            $column->decimal('uang_kerajinan', 15, 2)->default(0);
            $column->decimal('uang_extra_fooding', 15, 2)->default(0);
            $column->decimal('insentif_narik_jalur', 15, 2)->default(0);
            $column->decimal('uang_lembur', 15, 2)->default(0);
            $column->decimal('uang_piket', 15, 2)->default(0);
            
            // Potongan
            $column->decimal('potongan_kinerja', 15, 2)->default(0);
            $column->decimal('bpjs_ketenagakerjaan', 15, 2)->default(0);
            $column->decimal('potongan_pinjaman', 15, 2)->default(0);
            
            // Kalkulasi
            $column->decimal('total_penghasilan', 15, 2)->default(0);
            $column->decimal('total_potongan', 15, 2)->default(0);
            $column->decimal('gaji_bersih', 15, 2)->default(0);
            
            $column->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('employees');
    }
};
