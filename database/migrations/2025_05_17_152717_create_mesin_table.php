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
        Schema::create('mesin', function (Blueprint $table) {
            $table->id();
            
            // Informasi Dasar
            $table->string('nama_mesin');
            $table->string('tipe_mesin');
            $table->string('merek')->nullable();
            $table->string('model')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->string('status')->default('Aktif');
            $table->date('tanggal_pembelian')->nullable();
            $table->decimal('harga_pembelian', 15, 2)->nullable();
            $table->text('deskripsi')->nullable();
            
            // Spesifikasi Teknis
            $table->decimal('lebar_media_maksimum', 8, 2)->nullable();
            $table->json('detail_mesin')->nullable();
            // $table->string('gambar')->nullable();
            
            // Catatan
            $table->text('catatan_tambahan')->nullable();
            
            $table->string('cloudinary_public_id')->nullable();

            // Biaya Tinta
            $table->decimal('harga_tinta_per_liter', 15, 2)->nullable()->after('catatan_tambahan');
            $table->decimal('konsumsi_tinta_per_m2', 10, 2)->nullable()->after('harga_tinta_per_liter');
            
            // Biaya Tambahan (disimpan sebagai JSON)
            $table->json('biaya_tambahan')->nullable()->after('konsumsi_tinta_per_m2');
            
            $table->timestamps();
            $table->softDeletes(); // Untuk fitur "soft delete" agar data tidak benar-benar terhapus
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mesin');
    }
};
