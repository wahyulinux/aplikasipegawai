<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('bulan'); // pending, approved, rejected
            $table->timestamp('approved_at')->nullable()->after('status');
            $table->string('verification_code')->nullable()->unique()->after('gaji_bersih');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['status', 'approved_at', 'verification_code']);
        });
    }
};
