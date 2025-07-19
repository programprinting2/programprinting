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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('kode_produk')->unique();
            $table->unsignedBigInteger('kategori_utama_id');
            $table->unsignedBigInteger('sub_kategori_id')->nullable();
            $table->unsignedBigInteger('satuan_id');
            $table->enum('metode_penjualan', ['m2', 'meter_lari']);
            $table->integer('lebar')->default(0);
            $table->integer('panjang')->default(0);
            $table->boolean('status_aktif')->default(true);
            $table->json('bahan_baku_json')->nullable();
            $table->json('harga_bertingkat_json')->nullable();
            $table->json('harga_reseller_json')->nullable();
            $table->json('foto_pendukung_json')->nullable();
            $table->json('video_pendukung_json')->nullable();
            $table->json('dokumen_pendukung_json')->nullable();
            $table->json('alur_produksi_json')->nullable();
            $table->timestamps();

            $table->foreign('kategori_utama_id')->references('id')->on('detail_parameters')->onDelete('restrict');
            $table->foreign('sub_kategori_id')->references('id')->on('sub_detail_parameter')->onDelete('set null');
            $table->foreign('satuan_id')->references('id')->on('detail_parameters')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produk');
    }
};
