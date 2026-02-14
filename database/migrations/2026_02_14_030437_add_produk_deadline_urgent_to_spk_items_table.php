<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spk_items', function (Blueprint $table) {
            $table->foreignId('produk_id')->nullable()->after('spk_id')->constrained('produk')->nullOnDelete();
            $table->timestamp('deadline')->nullable()->after('panjang');
            $table->boolean('is_urgent')->default(false)->after('deadline');
        });
    }

    public function down(): void
    {
        Schema::table('spk_items', function (Blueprint $table) {
            $table->dropForeign(['produk_id']);
            $table->dropColumn(['produk_id', 'deadline', 'is_urgent']);
        });
    }
};