<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->boolean('is_acknowledged')->default(false)->after('approved_at');
            $table->timestamp('acknowledged_at')->nullable()->after('is_acknowledged');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['is_acknowledged', 'acknowledged_at']);
        });
    }
};
