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
        Schema::table('bahan_baku', function (Blueprint $table) {
            // Hapus kolom lama
            if (Schema::hasColumn('bahan_baku', 'kategori')) {
                $table->dropColumn('kategori');
            }
            // Tambahkan kolom baru
            $table->unsignedBigInteger('kategori_id')->nullable()->after('kode_bahan');
            $table->foreign('kategori_id')->references('id')->on('detail_parameters')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
            $table->string('kategori')->nullable();
        });
    }
};
