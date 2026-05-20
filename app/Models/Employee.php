<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nama',
        'email',
        'jabatan',
        'nomor_rekening',
    ];

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}
