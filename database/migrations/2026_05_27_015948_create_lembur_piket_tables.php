<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Overtimes (Lembur)
        Schema::create('overtimes', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->decimal('nominal_per_orang', 15, 2); // Snapshot
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_overtime', function (Blueprint $table) {
            $table->id();
            $table->foreignId('overtime_id')->constrained('overtimes')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel Pickets (Piket)
        Schema::create('pickets', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->decimal('nominal_per_orang', 15, 2); // Snapshot
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_picket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('picket_id')->constrained('pickets')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_picket');
        Schema::dropIfExists('pickets');
        Schema::dropIfExists('employee_overtime');
        Schema::dropIfExists('overtimes');
    }
};
