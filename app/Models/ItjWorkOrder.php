<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ItjWorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_wo',
        'tanggal_pengerjaan',
        'nominal_total',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pengerjaan' => 'date',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_itj')
                    ->withPivot('nominal_diterima')
                    ->withTimestamps();
    }
}
