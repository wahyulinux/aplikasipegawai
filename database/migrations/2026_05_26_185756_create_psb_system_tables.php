<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Settings
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Tabel PSB Work Orders
        Schema::create('psb_work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_wo')->unique();
            $table->date('tanggal_pengerjaan');
            $table->decimal('nominal_total', 15, 2); // Snapshot dari settings saat WO dibuat
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 3. Tabel Pivot Employee PSB (Many-to-Many)
        Schema::create('employee_psb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('psb_work_order_id')->constrained('psb_work_orders')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('nominal_diterima', 15, 2); // nominal_total / jumlah_pekerja
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_psb');
        Schema::dropIfExists('psb_work_orders');
        Schema::dropIfExists('settings');
    }
};
