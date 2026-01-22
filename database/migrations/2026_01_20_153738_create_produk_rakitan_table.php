<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('produk_rakitan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_rakitan_id')->constrained('produk')->onDelete('cascade');
            $table->foreignId('produk_komponen_id')->constrained('produk')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_snapshot', 15, 2)->default(0);
            $table->timestamp('harga_updated_at')->nullable();
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['produk_rakitan_id', 'produk_komponen_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('produk_rakitan');
    }
};