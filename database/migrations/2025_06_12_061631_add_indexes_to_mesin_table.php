<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('mesin', function (Blueprint $table) {
            // Tambahkan index untuk kolom yang sering digunakan untuk pencarian
            $table->index('nama_mesin');
            $table->index('merek');
            $table->index('model');
            $table->index('tipe_mesin');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('mesin', function (Blueprint $table) {
            // Hapus index yang ditambahkan
            $table->dropIndex(['nama_mesin']);
            $table->dropIndex(['merek']);
            $table->dropIndex(['model']);
            $table->dropIndex(['tipe_mesin']);
            $table->dropIndex(['status']);
        });
    }
};
