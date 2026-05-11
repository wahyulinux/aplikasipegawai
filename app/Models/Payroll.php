<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'bulan',
        'gaji_pokok',
        'tunjangan_jabatan',
        'uang_makan',
        'uang_kinerja',
        'uang_psb',
        'uang_kerajinan',
        'uang_extra_fooding',
        'insentif_narik_jalur',
        'uang_lembur',
        'uang_piket',
        'potongan_kinerja',
        'bpjs_ketenagakerjaan',
        'potongan_pinjaman',
    ];

    protected static function booted()
    {
        static::saving(function ($payroll) {
            // Hitung Total Penghasilan
            $payroll->total_penghasilan = 
                $payroll->gaji_pokok +
                $payroll->tunjangan_jabatan +
                $payroll->uang_makan +
                $payroll->uang_kinerja +
                $payroll->uang_psb +
                $payroll->uang_kerajinan +
                $payroll->uang_extra_fooding +
                $payroll->insentif_narik_jalur +
                $payroll->uang_lembur +
                $payroll->uang_piket;

            // Hitung Total Potongan
            $payroll->total_potongan = 
                $payroll->potongan_kinerja +
                $payroll->bpjs_ketenagakerjaan +
                $payroll->potongan_pinjaman;

            // Hitung Gaji Bersih
            $payroll->gaji_bersih = $payroll->total_penghasilan - $payroll->total_potongan;
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
