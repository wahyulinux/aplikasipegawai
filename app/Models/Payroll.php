<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'employee_id',
        'bulan',
        'status',
        'approved_by',
        'approved_at',
        'verification_code',
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
        'bpjs_kesehatan',
        'potongan_pinjaman',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($payroll) {
            $payroll->status = self::STATUS_PENDING;
        });

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
                $payroll->bpjs_kesehatan +
                $payroll->potongan_pinjaman;

            // Hitung Gaji Bersih
            $payroll->gaji_bersih = $payroll->total_penghasilan - $payroll->total_potongan;
            
            // Generate Verification Code if approved and not yet generated
            if ($payroll->status === self::STATUS_APPROVED && !$payroll->verification_code) {
                $payroll->verification_code = 'PAY-' . strtoupper(bin2hex(random_bytes(8)));
                $payroll->approved_at = now();
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
