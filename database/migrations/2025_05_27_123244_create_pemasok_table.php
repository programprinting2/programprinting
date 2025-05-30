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
        Schema::create('pemasok', function (Blueprint $table) {
            $table->id();

            // Info Umum
            $table->string('kode_pemasok')->unique();
            $table->string('nama');
            $table->string('no_telp')->nullable();
            $table->string('handphone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('kategori');
            $table->boolean('status')->default(true);

            // Alamat
            $table->integer('alamat_utama')->default(0);
            $table->json('alamat')->nullable();

            // Pembelian 
            $table->string('syarat_pembayaran')->nullable();
            $table->text('deskripsi_pembelian')->nullable();
            $table->decimal('default_diskon', 5, 2)->default(0);
            $table->enum('akun_utang', ['Utang Usaha', 'Utang Lain-lain']);
            $table->enum('akun_uang_muka', ['Uang Muka Pembelian', 'Kas', 'Bank']);

            //Pajak
            $table->string('nik')->nullable();
            $table->string('npwp')->nullable();
            $table->boolean('wajib_pajak')->nullable();


            // Rekening
            $table->integer('rekening_utama')->default(0);
            $table->json('rekening')->nullable();

            //Utang 
            $table->json('utang_awal')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pemasok');
    }
};
