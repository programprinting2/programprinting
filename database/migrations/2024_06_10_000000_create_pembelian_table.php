<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembelian')->unique();
            $table->date('tanggal_pembelian');
            $table->unsignedBigInteger('pemasok_id');
            // $table->enum('status', ['dipesan', 'diterima'])->default('dipesan');
            $table->string('nomor_form')->nullable();
            $table->date('jatuh_tempo')->nullable();
            $table->text('catatan')->nullable();
            $table->float('diskon_persen')->nullable();
            // $table->integer('jumlah_diskon')->nullable();
            $table->integer('biaya_pengiriman')->nullable();
            $table->float('tarif_pajak')->nullable();
            $table->integer('nota_kredit')->nullable();
            // $table->string('label_biaya_lain')->nullable();
            $table->integer('biaya_lain')->nullable();
            $table->integer('total')->nullable();
            $table->timestamps();

            $table->foreign('pemasok_id')->references('id')->on('pemasok')->onDelete('restrict');
        });
    }
    public function down()
    {
        Schema::dropIfExists('pembelian');
    }
}; 