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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('id_karyawan', 9)->unique();
            $table->string('nama_lengkap');
            $table->string('posisi');
            $table->string('departemen');
            $table->date('tanggal_masuk');
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai'])->nullable();
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('gaji_pokok')->default(0);
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->string('npwp')->nullable();
            $table->string('status_pajak')->nullable();
            $table->integer('tarif_pajak')->default(0);
            $table->integer('estimasi_hari_kerja')->default(0);
            $table->integer('jam_kerja_per_hari')->default(0);
            $table->json('alamat')->nullable();
            $table->json('rekening')->nullable();
            $table->integer('alamat_utama')->default(0);
            $table->integer('rekening_utama')->default(0);
            $table->json('komponen_gaji')->nullable();
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
        Schema::dropIfExists('karyawan');
    }
};
