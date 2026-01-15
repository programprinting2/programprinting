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
        Schema::table('produk', function (Blueprint $table) {
            $table->json('biaya_tambahan_json')->nullable()->after('spesifikasi_teknis_json');
            $table->decimal('total_modal_keseluruhan', 15, 2)->default(0)->after('biaya_tambahan_json');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn(['biaya_tambahan_json', 'total_modal_keseluruhan']);
        });
    }
};
