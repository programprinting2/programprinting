<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('item_pembelian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembelian_id');
            $table->unsignedBigInteger('bahanbaku_id');
            $table->integer('jumlah');
            $table->integer('harga');
            $table->float('diskon_persen')->nullable();
            $table->integer('subtotal');
            $table->timestamps();

            $table->foreign('pembelian_id')->references('id')->on('pembelian')->onDelete('cascade');
            $table->foreign('bahanbaku_id')->references('id')->on('bahan_baku')->onDelete('restrict');
        });
    }
    public function down()
    {
        Schema::dropIfExists('pembelian_item');
    }
}; 