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
            // Hapus kolom lama
            if (Schema::hasColumn('bahan_baku', 'satuan_utama')) {
                $table->dropColumn('satuan_utama');
            }
            // Tambahkan kolom baru
            $table->unsignedBigInteger('satuan_utama_id')->nullable()->after('sub_kategori_id');
            $table->foreign('satuan_utama_id')->references('id')->on('detail_parameters')->onDelete('set null');
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
            $table->dropForeign(['satuan_utama_id']);
            $table->dropColumn('satuan_utama_id');
            $table->string('satuan_utama')->nullable();
        });
    }
};
