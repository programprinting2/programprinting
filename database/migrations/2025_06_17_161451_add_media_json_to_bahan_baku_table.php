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
    public function up()
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            // Ubah nama kolom foto_produk_url menjadi foto_pendukung_json
            $table->json('foto_pendukung_json')->nullable()->after('foto_produk_url');
            // Hapus kolom foto_produk_url yang lama
            $table->dropColumn('foto_produk_url');
            // Tambahkan kolom video_pendukung_json
            $table->json('video_pendukung_json')->nullable()->after('foto_pendukung_json');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            // Kembalikan kolom foto_produk_url jika rollback
            $table->string('foto_produk_url')->nullable()->after('foto_pendukung_json');
            // Hapus kolom foto_pendukung_json
            $table->dropColumn('foto_pendukung_json');
            // Hapus kolom video_pendukung_json
            $table->dropColumn('video_pendukung_json');
        });
    }
};
