<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pelanggan')->unique();
            $table->string('nama');
            // $table->enum('kategori', ['Umum', 'Retail', 'Distributor', 'Agen', 'Grosir', 'Korporasi']);
            $table->string('no_telp')->nullable();
            $table->string('handphone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            // $table->string('faksimili')->nullable();
            $table->string('no_whatsapp')->nullable();

            // Alamat utama
            $table->integer('alamat_utama')->default(0);
            $table->json('alamat')->nullable(); // Menyimpan multiple alamat
            // $table->text('alamat')->nullable();
            // $table->string('kota')->nullable();
            // $table->string('provinsi')->nullable();
            // $table->string('kode_pos')->nullable();
            // $table->string('negara')->default('Indonesia');

            // Data penjualan
            $table->string('kategori_harga')->nullable();
            // $table->string('kategori_diskon')->nullable();
            $table->string('syarat_pembayaran')->nullable();
            // $table->string('default_penjual')->nullable();
            $table->decimal('default_diskon', 5, 2)->default(0);
            $table->text('default_deskripsi')->nullable();
            // $table->boolean('kirim_barang')->default(false);

            // Data pajak
            $table->string('nik')->nullable();
            $table->string('npwp')->nullable();
            $table->boolean('wajib_pajak')->nullable();
            $table->string('nitku')->nullable();
            $table->string('kode_negara')->nullable();
            $table->string('tipe_transaksi')->nullable();
            $table->string('detail_transaksi')->nullable();
            $table->boolean('default_total_faktur_pajak')->default(false);
            
            // Pembatasan piutang
            $table->json('data_lain')->nullable();
            // $table->enum('tipe_pembatasan', ['Per Pelanggan', 'Tergabung ke Pelanggan Induk'])->default('Per Pelanggan');
            // $table->integer('batas_umur_faktur')->nullable();
            // $table->decimal('batas_total_piutang', 15, 2)->nullable();
            // $table->string('gudang_default')->nullable();
            // $table->text('catatan')->nullable();

            // Data JSON
            $table->json('kontak')->nullable(); // Menyimpan multiple kontak            
            $table->json('piutang_awal')->nullable(); // Menyimpan data piutang awal
            // $table->json('akun_penjualan')->nullable(); // Menyimpan data akun penjualan, piutang, uang muka

            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pelanggan');
    }
};
