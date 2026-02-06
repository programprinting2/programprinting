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
        Schema::table('produk_bahan_baku', function (Blueprint $table) {
            $table->decimal('panjang', 10, 2)->nullable()->after('harga_snapshot')
                  ->comment('Panjang bahan baku per unit produk (untuk bahan metric)');
            $table->decimal('lebar', 10, 2)->nullable()->after('panjang')
                  ->comment('Lebar bahan baku per unit produk (untuk bahan metric)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_bahan_baku', function (Blueprint $table) {
            $table->dropColumn(['panjang', 'lebar']);
        });
    }
};