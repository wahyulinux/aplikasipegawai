<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Picket extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'nominal_per_orang',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_picket')
                    ->withTimestamps();
    }
}
