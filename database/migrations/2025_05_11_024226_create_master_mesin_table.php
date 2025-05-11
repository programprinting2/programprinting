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
        Schema::create('master_mesin', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mesin')->unique();
            $table->string('nama_mesin');
            $table->string('model_mesin');
            $table->string('jenis_mesin');
            $table->text('keterangan')->nullable();
            $table->boolean('non_produksi')->default(false);
            $table->date('tanggal_beli');
            $table->string('gambar')->nullable();
            $table->boolean('aktif')->default(true);
            $table->string('nomor_seri')->nullable();
            $table->string('pabrikan')->nullable();
            $table->string('lokasi_pemeliharaan')->nullable();
            $table->date('tanggal_pemeliharaan_terakhir')->nullable();
            $table->date('tanggal_pemeliharaan_selanjutnya')->nullable();
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('master_mesin');
    }
};
