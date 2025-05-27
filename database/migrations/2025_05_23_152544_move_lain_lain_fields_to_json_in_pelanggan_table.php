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
        Schema::table('pelanggan', function (Blueprint $table) {
            // Hapus kolom lama dari tab Lain-lain
            $table->dropColumn([
                'tipe_pembatasan',
                'batas_umur_faktur',
                'batas_total_piutang',
                'gudang_default',
                'catatan',
            ]);
            
            // Tambahkan kolom baru untuk data JSON Lain-lain
            $table->json('data_lain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            // Hapus kolom data_lain
            $table->dropColumn('data_lain');

            // Tambahkan kembali kolom lama
            $table->enum('tipe_pembatasan', ['Per Pelanggan', 'Tergabung ke Pelanggan Induk'])->default('Per Pelanggan');
            $table->integer('batas_umur_faktur')->nullable();
            $table->decimal('batas_total_piutang', 15, 2)->nullable();
            $table->string('gudang_default')->nullable();
            $table->text('catatan')->nullable();
        });
    }
};
