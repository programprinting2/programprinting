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
            // Hapus kolom alamat utama yang terpisah
            $table->dropColumn(['alamat', 'kota', 'provinsi', 'kode_pos', 'negara']);
            
            // Ganti nama kolom alamat_lain menjadi alamat
            $table->renameColumn('alamat_lain', 'alamat');
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
            // Kembalikan nama kolom alamat menjadi alamat_lain
            $table->renameColumn('alamat', 'alamat_lain');

            // Tambahkan kembali kolom alamat utama yang terpisah
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('negara')->default('Indonesia');
        });
    }
};
