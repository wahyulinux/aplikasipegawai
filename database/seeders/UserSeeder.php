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
        User::firstOrCreate(
            ['email' => 'admin@payroll.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
            ]
        );

        // Buat User Staff (Maker)
        User::firstOrCreate(
            ['email' => 'staff@payroll.com'],
            [
                'name' => 'Staff Penginput',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        // Buat User HRD (Approver)
        User::firstOrCreate(
            ['email' => 'hrd@payroll.com'],
            [
                'name' => 'HRD Manager',
                'password' => Hash::make('password'),
                'role' => 'hrd',
            ]
        );

        // Buat User Finance (Approver Pinjaman)
        User::firstOrCreate(
            ['email' => 'finance@payroll.com'],
            [
                'name' => 'Finance Manager',
                'password' => Hash::make('password'),
                'role' => 'finance',
            ]
        );
    }
}
