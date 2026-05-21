<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Superadmin (System Administrator)
        User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@payroll.com',
            'password' => Hash::make('admin123'),
            'role' => 'superadmin',
        ]);

        // Buat User Staff (Maker)
        User::create([
            'name' => 'Staff Penginput',
            'email' => 'staff@payroll.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // Buat User HRD (Approver)
        User::create([
            'name' => 'HRD Manager',
            'email' => 'hrd@payroll.com',
            'password' => Hash::make('password'),
            'role' => 'hrd',
        ]);

        // Buat User Finance (Approver Pinjaman)
        User::create([
            'name' => 'Finance Manager',
            'email' => 'finance@payroll.com',
            'password' => Hash::make('password'),
            'role' => 'finance',
        ]);
    }
}
