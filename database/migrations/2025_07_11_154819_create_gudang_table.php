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
        Schema::create('gudang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_gudang', 20)->unique();
            $table->string('nama_gudang', 100);
            $table->string('manager', 100);
            $table->integer('kapasitas');
            $table->enum('status', ['Aktif', 'Tidak Aktif']);
            $table->text('deskripsi')->nullable();
            $table->text('alamat');
            $table->string('kota', 50);
            $table->string('provinsi', 50);
            $table->string('kode_pos', 10);
            $table->string('no_telepon', 20);
            $table->string('email', 100)->nullable();
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
        Schema::dropIfExists('gudang');
    }
};
