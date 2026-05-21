<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->decimal('nominal_pinjaman', 15, 2);
            $table->integer('tenor_bulan');
            $table->decimal('nominal_cicilan', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->text('keterangan')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, lunas
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
