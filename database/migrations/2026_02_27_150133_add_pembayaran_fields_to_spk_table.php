<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spk', function (Blueprint $table) {
            $table->decimal('total_dibayar', 15, 2)->default(0)->after('total_biaya');
            $table->string('status_pembayaran', 20)->default('belum_bayar')->after('total_dibayar');
        });
    }

    public function down(): void
    {
        Schema::table('spk', function (Blueprint $table) {
            $table->dropColumn(['total_dibayar', 'status_pembayaran']);
        });
    }
};