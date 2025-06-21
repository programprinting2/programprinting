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
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bahan')->unique();
            $table->string('nama_bahan');
            $table->text('keterangan')->nullable();
            $table->json('detail_spesifikasi_json');
            $table->string('kategori')->nullable();
            // $table->string('sub_kategori')->nullable();
            $table->unsignedBigInteger('sub_kategori_id')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->string('satuan_utama')->nullable();
            // $table->string('pilihan_warna')->nullable();
            // $table->string('nama_warna_custom')->nullable();
            // $table->decimal('berat', 10, 2)->nullable();
            // $table->decimal('tinggi', 10, 2)->nullable();
            // $table->decimal('tebal', 10, 2)->nullable();
            // $table->decimal('gramasi_densitas', 10, 2)->nullable();
            // $table->decimal('volume', 10, 2)->nullable();
            $table->json('konversi_satuan_json')->nullable();
            $table->unsignedBigInteger('pemasok_utama_id')->nullable();
            $table->integer('harga_terakhir')->nullable();
            // $table->json('histori_harga_json')->nullable();
            $table->integer('stok_saat_ini')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->integer('stok_maksimum')->default(0);
            // $table->string('foto_produk_url')->nullable();
            $table->json('foto_pendukung_json')->nullable();
            $table->json('video_pendukung_json')->nullable();
            $table->json('dokumen_pendukung_json')->nullable();
            $table->json('link_pendukung_json')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('pemasok_utama_id')->references('id')->on('pemasok')->onDelete('set null');
            $table->foreign('sub_kategori_id')->references('id')->on('detail_parameters')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bahan_baku');
    }
};
