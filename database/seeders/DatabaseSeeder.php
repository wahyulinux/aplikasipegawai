<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);

        \App\Models\Setting::firstOrCreate(
            ['key' => 'psb_nominal_default'],
            [
                'value' => '60000',
                'description' => 'Tarif dasar per 1 Work Order PSB (akan dibagi rata jika dikerjakan >1 orang)',
            ]
        );

        \App\Models\Setting::firstOrCreate(
            ['key' => 'itj_nominal_default'],
            [
                'value' => '100000',
                'description' => 'Tarif dasar per 1 Work Order Tarik Jalur / ITJ (akan dibagi rata, tanpa batas maksimal pekerja)',
            ]
        );

        \App\Models\Setting::firstOrCreate(
            ['key' => 'bpjs_ketenagakerjaan_default'],
            [
                'value' => '0',
                'description' => 'Potongan default BPJS Ketenagakerjaan per bulan',
            ]
        );

        \App\Models\Setting::firstOrCreate(
            ['key' => 'bpjs_kesehatan_default'],
            [
                'value' => '0',
                'description' => 'Potongan default BPJS Kesehatan per bulan',
            ]
        );
    }
}
